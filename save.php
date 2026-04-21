<?php
header('Content-Type: application/json');

require_once __DIR__ . '/template-registry.php';

function buildConfessionUrl($id) {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
    $scriptDir = rtrim(str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/save.php')), '/');
    $basePath = $scriptDir === '' || $scriptDir === '.' ? '' : $scriptDir;

    return sprintf('%s://%s%s/view.php?id=%s', $scheme, $host, $basePath, rawurlencode($id));
}

function sanitizeString($value, $maxLength) {
    $value = (string) ($value ?? '');
    $value = trim($value);
    $value = mb_substr($value, 0, $maxLength, 'UTF-8');
    $value = htmlspecialchars($value, ENT_QUOTES | ENT_HTML5, 'UTF-8');
    return $value;
}

function verifyTurnstileToken($token, $remoteIp) {
    $secretKey = 'your_secretkey_here';

    if ($token === '') {
        return ['success' => false, 'error' => '请先完成人机验证'];
    }

    if (!function_exists('curl_init')) {
        return ['success' => false, 'error' => '当前服务器未启用 curl，无法校验 Turnstile'];
    }

    $ch = curl_init('https://challenges.cloudflare.com/turnstile/v0/siteverify');
    if ($ch === false) {
        return ['success' => false, 'error' => '初始化验证请求失败'];
    }

    $payload = [
        'secret' => $secretKey,
        'response' => $token,
        'remoteip' => $remoteIp,
    ];

    curl_setopt_array($ch, [
        CURLOPT_POST => true,
        CURLOPT_POSTFIELDS => http_build_query($payload),
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_TIMEOUT => 10,
        CURLOPT_HTTPHEADER => ['Content-Type: application/x-www-form-urlencoded'],
    ]);

    $response = curl_exec($ch);
    if ($response === false) {
        $error = curl_error($ch);
        curl_close($ch);
        return ['success' => false, 'error' => 'Turnstile 验证请求失败：' . $error];
    }

    $httpStatus = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    if ($httpStatus < 200 || $httpStatus >= 300) {
        return ['success' => false, 'error' => 'Turnstile 验证服务响应异常'];
    }

    $data = json_decode($response, true);
    if (!is_array($data) || !($data['success'] ?? false)) {
        return ['success' => false, 'error' => '人机验证未通过，请重试'];
    }

    return ['success' => true, 'error' => ''];
}

session_start();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'error' => 'Method not allowed']);
    exit;
}

$maxRequests = 10;
$windowSeconds = 60;
$now = time();
$rateKey = 'rate_' . ($_SERVER['REMOTE_ADDR'] ?? 'unknown');

if (!isset($_SESSION[$rateKey])) {
    $_SESSION[$rateKey] = [];
}

$_SESSION[$rateKey] = array_filter($_SESSION[$rateKey], function ($timestamp) use ($now, $windowSeconds) {
    return ($now - $timestamp) < $windowSeconds;
});

if (count($_SESSION[$rateKey]) >= $maxRequests) {
    http_response_code(429);
    echo json_encode(['success' => false, 'error' => '请求过于频繁，请稍后再试']);
    exit;
}

$_SESSION[$rateKey][] = $now;

$json = file_get_contents('php://input');

if (strlen($json) > 10240) {
    echo json_encode(['success' => false, 'error' => '请求数据过大']);
    exit;
}

$data = json_decode($json, true);

if (json_last_error() !== JSON_ERROR_NONE) {
    echo json_encode(['success' => false, 'error' => 'Invalid JSON format']);
    exit;
}

$recipient = sanitizeString($data['recipient'] ?? '', 40);
$sender = sanitizeString($data['sender'] ?? '', 40);
$message = sanitizeString($data['message'] ?? '', 500);
$bgColor = sanitizeString($data['bgColor'] ?? 'love-primary', 20);
$password = sanitizeString($data['password'] ?? '', 60);
$template = sanitizeString($data['template'] ?? 'default', 30);
$turnstileToken = trim((string) ($data['turnstileToken'] ?? ''));
$ip = $_SERVER['REMOTE_ADDR'] ?? '';

if (empty($recipient) || empty($sender) || empty($message)) {
    echo json_encode(['success' => false, 'error' => '请填写所有必填字段']);
    exit;
}

$turnstileResult = verifyTurnstileToken($turnstileToken, $ip);
if (!$turnstileResult['success']) {
    http_response_code(400);
    echo json_encode(['success' => false, 'error' => $turnstileResult['error']]);
    exit;
}

$allowedBgColors = ['love-primary', 'love-secondary', 'love-accent', 'love-dark'];
if (!in_array($bgColor, $allowedBgColors, true)) {
    $bgColor = 'love-primary';
}

$templates = scanTemplates();
$templateMap = getTemplateMap($templates);
$template = isset($templateMap[$template]) ? $template : (($defaultTemplate = getDefaultTemplate($templates)) ? $defaultTemplate['key'] : 'default');

$id = bin2hex(random_bytes(12));

$saveData = [
    'id' => $id,
    'recipient' => $recipient,
    'sender' => $sender,
    'message' => $message,
    'bgColor' => $bgColor,
    'template' => $template,
    'timestamp' => date('Y-m-d H:i:s'),
    'ip' => $ip,
    'password' => $password
];

$confessionsDir = __DIR__ . '/confessions';

if (!is_dir($confessionsDir)) {
    mkdir($confessionsDir, 0755, true);
}

$jsonFilePath = $confessionsDir . '/' . $id . '.json';

if (file_put_contents($jsonFilePath, json_encode($saveData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE))) {
    echo json_encode([
        'success' => true,
        'id' => $id,
        'url' => buildConfessionUrl($id)
    ]);
} else {
    http_response_code(500);
    echo json_encode(['success' => false, 'error' => 'Failed to save confession']);
}
