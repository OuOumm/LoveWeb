<?php
header('Content-Type: text/html; charset=utf-8');

require_once __DIR__ . '/template-registry.php';

function hexToLightenColor($hex, $percent) {
    $hex = str_replace('#', '', $hex);
    $r = hexdec(substr($hex, 0, 2));
    $g = hexdec(substr($hex, 2, 2));
    $b = hexdec(substr($hex, 4, 2));

    $r = min(255, $r + (255 - $r) * $percent);
    $g = min(255, $g + (255 - $g) * $percent);
    $b = min(255, $b + (255 - $b) * $percent);

    return sprintf('#%02x%02x%02x', round($r), round($g), round($b));
}

function renderIcon($name, $class = 'h-5 w-5') {
    $icons = [
        'arrow-left' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 19.5L8.25 12l7.5-7.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12h12" />',
        'home' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 12l8.954-8.955a1.125 1.125 0 011.591 0L21.75 12" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 9.75V19.5A2.25 2.25 0 006.75 21.75h10.5a2.25 2.25 0 002.25-2.25V9.75" />',
        'lock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V7.875a4.125 4.125 0 10-8.25 0V10.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.625 10.5h12.75A1.875 1.875 0 0120.25 12.375v6A1.875 1.875 0 0118.375 20.25H5.625A1.875 1.875 0 013.75 18.375v-6A1.875 1.875 0 015.625 10.5z" />',
        'key' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 7.5a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 11.25L4.5 18.75" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 16.5l1.5 1.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 18.75l1.5 1.5" />',
        'unlock' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M16.5 10.5V8.625a4.125 4.125 0 10-8.25 0" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.625 10.5h12.75A1.875 1.875 0 0120.25 12.375v6A1.875 1.875 0 0118.375 20.25H5.625A1.875 1.875 0 013.75 18.375v-6A1.875 1.875 0 015.625 10.5z" />',
        'heart' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.435 6.582a5.625 5.625 0 00-7.955 0L12 8.06l-1.48-1.478a5.625 5.625 0 00-7.955 7.955l1.48 1.478L12 21.94l7.955-7.925 1.48-1.478a5.625 5.625 0 000-7.955z" />',
        'heart-broken' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 20.25l-1.56-1.39C5.153 14.14 2.25 11.54 2.25 7.875A4.875 4.875 0 017.125 3c1.5 0 2.889.676 3.825 1.83L12 6l1.05-1.17A5.156 5.156 0 0116.875 3a4.875 4.875 0 014.875 4.875c0 3.665-2.903 6.265-8.19 10.985L12 20.25z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12.75 6.75l-1.5 3 2.25 1.5-1.5 3" />',
        'quote-left' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M10.5 8.25H6.75A2.25 2.25 0 004.5 10.5v3.75A2.25 2.25 0 006.75 16.5h3.75A2.25 2.25 0 0012.75 14.25V6.75A4.5 4.5 0 008.25 2.25" />',
        'quote-right' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M13.5 8.25h3.75a2.25 2.25 0 012.25 2.25v3.75a2.25 2.25 0 01-2.25 2.25H13.5a2.25 2.25 0 01-2.25-2.25V6.75a4.5 4.5 0 014.5-4.5" />',
        'calendar' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M6.75 3v2.25M17.25 3v2.25M3.75 8.25h16.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M4.5 5.25h15A1.5 1.5 0 0121 6.75v11.25A1.5 1.5 0 0119.5 19.5h-15A1.5 1.5 0 013 18V6.75a1.5 1.5 0 011.5-1.5z" />',
        'sparkles' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9.813 15.904L9 18l-.813-2.096a2.25 2.25 0 00-1.29-1.29L4.8 13.8l2.096-.813a2.25 2.25 0 001.29-1.29L9 9.6l.813 2.096a2.25 2.25 0 001.29 1.29l2.096.813-2.096.813a2.25 2.25 0 00-1.29 1.29z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M18.259 8.715L18 9.75l-.259-1.035a1.875 1.875 0 00-1.356-1.356L15.35 7.1l1.035-.259a1.875 1.875 0 001.356-1.356L18 4.45l.259 1.035a1.875 1.875 0 001.356 1.356l1.035.259-1.035.259a1.875 1.875 0 00-1.356 1.356z" />',
        'weibo' => '<path fill="currentColor" d="M9.03 18.47c-4.01 0-7.27-2.42-7.27-5.4 0-2.15 1.67-3.99 4.08-4.86-.17-.67-.3-1.56.08-2.28.52-.99 1.95-1.18 2.93-.83.7.25 1.16.77 1.46 1.31.88-.29 1.82-.45 2.8-.45 4.01 0 7.27 2.42 7.27 5.4s-3.26 5.4-7.27 5.4c-.63 0-1.24-.06-1.82-.18-.83 1.1-2.05 1.76-3.26 1.76Zm-.57-9.03c-2.1 0-3.92 1.19-3.92 2.85 0 1.7 1.82 2.9 4.15 2.9 2.34 0 4.15-1.2 4.15-2.9 0-1.66-1.8-2.85-4.38-2.85Zm6.83-4.68c1.87.17 3.35 1.61 3.54 3.46a.86.86 0 001.7-.18 5.78 5.78 0 00-5.08-4.97.86.86 0 00-.16 1.69Zm-1.44 2.18c.95.1 1.72.86 1.83 1.8a.72.72 0 001.43-.14 3.92 3.92 0 00-3.11-3.03.72.72 0 00-.15 1.37Z"/>',
        'wechat' => '<path fill="currentColor" d="M8.86 4.27C4.8 4.27 1.5 6.93 1.5 10.22c0 1.9 1.11 3.58 2.85 4.67l-.72 2.52 2.8-1.4c.76.2 1.57.3 2.43.3 4.06 0 7.36-2.66 7.36-5.95S12.92 4.27 8.86 4.27Zm-2.72 4.89a.78.78 0 110-1.56.78.78 0 010 1.56Zm5.39 0a.78.78 0 110-1.56.78.78 0 010 1.56Zm8.61 5.42c0-2.72-2.72-4.92-6.07-4.92S8 11.86 8 14.58s2.72 4.92 6.07 4.92c.66 0 1.29-.09 1.88-.25l2.3 1.14-.59-2.04c1.51-.88 2.48-2.23 2.48-3.77Zm-8.16-.83a.67.67 0 110-1.34.67.67 0 010 1.34Zm4.17 0a.67.67 0 110-1.34.67.67 0 010 1.34Z"/>',
        'qq' => '<path fill="currentColor" d="M12 2.25c-2.68 0-4.87 2.18-4.87 4.87 0 1.3.5 2.48 1.32 3.36-.1.35-.15.73-.15 1.13 0 .77.2 1.48.55 2.1-.32.27-.73.61-1.2 1-.73.62-.87 1.49-.37 2.1.43.53 1.26.74 2.03.2l1.18-.82c.47.17.98.26 1.51.26s1.04-.09 1.51-.26l1.18.82c.77.54 1.6.33 2.03-.2.5-.61.36-1.48-.37-2.1-.47-.39-.88-.73-1.2-1 .35-.62.55-1.33.55-2.1 0-.4-.05-.78-.15-1.13a4.83 4.83 0 001.32-3.36C16.87 4.43 14.68 2.25 12 2.25Z"/>',
        'email' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M21.75 7.5v9A2.25 2.25 0 0119.5 18.75h-15A2.25 2.25 0 012.25 16.5v-9" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M2.25 7.5l8.542 5.694a2.25 2.25 0 002.416 0L21.75 7.5" />',
        'copy' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M15.75 17.25h3A2.25 2.25 0 0021 15V5.25A2.25 2.25 0 0018.75 3h-9.5A2.25 2.25 0 007 5.25v3" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M5.25 7.5h9.5A2.25 2.25 0 0117 9.75v9A2.25 2.25 0 0114.75 21h-9.5A2.25 2.25 0 013 18.75v-9A2.25 2.25 0 015.25 7.5z" />',
        'music-note' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 18.75a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 18.75V6.75l10.5-2.25v11.25" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M19.5 15.75a2.25 2.25 0 11-4.5 0 2.25 2.25 0 014.5 0z" />',
        'shield-check' => '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M9 12.75l2.25 2.25 3.75-4.5" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.8" d="M12 3l7.5 3v6c0 5.25-3.75 8.25-7.5 9-3.75-.75-7.5-3.75-7.5-9V6l7.5-3z" />'
    ];

    $paths = $icons[$name] ?? $icons['heart'];
    return '<svg class="' . htmlspecialchars($class, ENT_QUOTES, 'UTF-8') . '" viewBox="0 0 24 24" fill="none" stroke="currentColor" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">' . $paths . '</svg>';
}

function renderAppShellStart($pageTitle, $bodyClass = '') {
    ?>
<!DOCTYPE html>
<html lang="zh-CN">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'); ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script>
        tailwind.config = {
            theme: {
                extend: {
                    colors: {
                        love: {
                            primary: '#FF4B91',
                            secondary: '#FF85B3',
                            accent: '#FFC1D6',
                            blush: '#FFF3F8',
                            light: '#FFF7FB',
                            dark: '#C2185B',
                            ink: '#3D1631'
                        }
                    },
                    boxShadow: {
                        romantic: '0 20px 60px rgba(255, 75, 145, 0.18)',
                        soft: '0 12px 30px rgba(148, 163, 184, 0.14)'
                    },
                    fontFamily: {
                        romantic: ['Dancing Script', 'cursive'],
                        sans: ['"Inter"', '"PingFang SC"', '"Microsoft YaHei"', 'sans-serif']
                    },
                    animation: {
                        float: 'float 4.5s ease-in-out infinite',
                        fadeIn: 'fadeIn .8s ease forwards',
                        pulseSoft: 'pulseSoft 3s ease-in-out infinite'
                    },
                    keyframes: {
                        float: {
                            '0%, 100%': { transform: 'translateY(0)' },
                            '50%': { transform: 'translateY(-10px)' }
                        },
                        fadeIn: {
                            '0%': { opacity: '0', transform: 'translateY(18px)' },
                            '100%': { opacity: '1', transform: 'translateY(0)' }
                        },
                        pulseSoft: {
                            '0%, 100%': { opacity: '.8', transform: 'scale(1)' },
                            '50%': { opacity: '1', transform: 'scale(1.03)' }
                        },
                        heartRise: {
                            '0%': { transform: 'translate3d(0,0,0) rotate(45deg) scale(.8)', opacity: '.0' },
                            '10%': { opacity: '.45' },
                            '100%': { transform: 'translate3d(var(--drift), -105vh, 0) rotate(45deg) scale(1.05)', opacity: '0' }
                        },
                        shake: {
                            '10%, 90%': { transform: 'translate3d(-1px, 0, 0)' },
                            '20%, 80%': { transform: 'translate3d(2px, 0, 0)' },
                            '30%, 50%, 70%': { transform: 'translate3d(-4px, 0, 0)' },
                            '40%, 60%': { transform: 'translate3d(4px, 0, 0)' }
                        }
                    }
                }
            }
        }
    </script>
    <style type="text/tailwindcss">
        @layer utilities {
            .ring-focus {
                @apply focus:outline-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-love-primary/70 focus-visible:ring-offset-2 focus-visible:ring-offset-white;
            }
            .glass-panel {
                @apply border border-white/70 bg-white/85 backdrop-blur-xl;
            }
            .hero-orb {
                position: absolute;
                border-radius: 9999px;
                filter: blur(10px);
                opacity: .45;
                pointer-events: none;
            }
            .heart-particle {
                position: absolute;
                width: 16px;
                height: 16px;
                background: rgba(255, 75, 145, 0.25);
                transform: rotate(45deg);
                animation: heartRise linear forwards;
                pointer-events: none;
            }
            .heart-particle::before,
            .heart-particle::after {
                content: '';
                position: absolute;
                width: 16px;
                height: 16px;
                border-radius: 9999px;
                background: inherit;
            }
            .heart-particle::before { top: -8px; left: 0; }
            .heart-particle::after { left: -8px; top: 0; }
            .copy-glow {
                box-shadow: 0 0 0 4px rgba(255, 75, 145, 0.1);
            }
            .message-prose p {
                margin-bottom: 1.1rem;
            }
            .message-prose p:last-child {
                margin-bottom: 0;
            }
            .paper-grid {
                background-image: linear-gradient(rgba(255, 75, 145, 0.06) 1px, transparent 1px), linear-gradient(90deg, rgba(255, 75, 145, 0.06) 1px, transparent 1px);
                background-size: 28px 28px;
            }
            @media (prefers-reduced-motion: reduce) {
                *, *::before, *::after {
                    animation-duration: 0.01ms !important;
                    animation-iteration-count: 1 !important;
                    transition-duration: 0.01ms !important;
                    scroll-behavior: auto !important;
                }
            }
        }
    </style>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Dancing+Script:wght@500;600;700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">
</head>
<body class="min-h-screen overflow-x-hidden bg-[radial-gradient(circle_at_top,_rgba(255,193,214,0.65),_rgba(255,247,251,0.92)_36%,_#fff_100%)] text-slate-900 <?php echo htmlspecialchars($bodyClass, ENT_QUOTES, 'UTF-8'); ?>">
<?php
}

function renderAppShellEnd() {
    ?>
</body>
</html>
<?php
}

function loadTemplateConfig($templateName) {
    $template = resolveTemplate($templateName);
    if ($template === null) {
        return [
            'templateName' => 'default',
            'config' => []
        ];
    }

    $config = is_file($template['configPath']) ? require $template['configPath'] : [];
    return [
        'templateName' => $template['key'],
        'config' => is_array($config) ? $config : []
    ];
}

function loadTemplateHtml($templateName) {
    $template = resolveTemplate($templateName);
    if ($template === null) {
        return '';
    }

    return is_file($template['templatePath']) ? (string) file_get_contents($template['templatePath']) : '';
}

function renderTemplateHtml($templateHtml, array $variables) {
    $rendered = preg_replace_callback('/\{\{\s*([a-zA-Z0-9_]+)\s*\}\}/', function ($matches) use ($variables) {
        $key = $matches[1];
        return array_key_exists($key, $variables) ? (string) $variables[$key] : '';
    }, $templateHtml);

    return $rendered ?? '';
}

function buildCurrentUrl() {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && (string) $_SERVER['SERVER_PORT'] === '443');
    $scheme = $https ? 'https' : 'http';
    $host = $_SERVER['HTTP_HOST'] ?? ($_SERVER['SERVER_NAME'] ?? 'localhost');
    $requestUri = $_SERVER['REQUEST_URI'] ?? '/view.php';

    return $scheme . '://' . $host . $requestUri;
}

$id = isset($_GET['id']) ? trim((string) $_GET['id']) : '';
$example = isset($_GET['example']) ? intval($_GET['example']) : 0;

if ($id === '' && $example === 0) {
    header('Location: /index.html');
    exit;
}

$confession = [];
if ($id !== '') {
    $filename = __DIR__ . '/confessions/' . basename($id) . '.json';
    if (is_file($filename)) {
        $json = file_get_contents($filename);
        $confession = json_decode($json ?: '', true) ?: [];
    }
} elseif ($example !== 0) {
    $examples = [
        1 => [
            'recipient' => '亲爱的',
            'sender' => '爱你的人',
            'message' => '遇见你是最美丽的意外。每一天爱你都比昨天更多一点。愿我们的爱情像星辰一样永恒，像花朵一样绽放。',
            'bgColor' => 'love-primary',
            'template' => 'default',
            'timestamp' => date('Y-m-d H:i:s')
        ],
        2 => [
            'recipient' => '宝贝',
            'sender' => '小明',
            'message' => '从相识的那一刻起，我的心就被你占据。你的笑容是我每天最期待的阳光，你的陪伴是我最大的幸福。感谢你出现在我的生命里，让我明白了爱的真谛。',
            'bgColor' => 'love-secondary',
            'template' => 'romantic',
            'timestamp' => date('Y-m-d H:i:s')
        ],
        3 => [
            'recipient' => '亲爱的',
            'sender' => '未知',
            'message' => '我承诺，无论顺境还是逆境，无论富裕还是贫穷，无论健康还是疾病，我都会永远爱你、珍惜你，直到永远。这份爱不会因时间褪色，不会因距离改变。',
            'bgColor' => 'love-dark',
            'template' => 'default',
            'timestamp' => date('Y-m-d H:i:s')
        ]
    ];
    $confession = $examples[$example] ?? [];
}

if ($confession === []) {
    http_response_code(404);
    renderAppShellStart('告白不存在 - 爱在指尖');
    ?>
    <main class="relative isolate flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="hero-orb left-[-5rem] top-[12%] h-44 w-44 bg-love-secondary/60"></div>
        <div class="hero-orb bottom-[8%] right-[-4rem] h-56 w-56 bg-fuchsia-200/50"></div>
        <section class="glass-panel relative z-10 w-full max-w-xl rounded-[2rem] p-8 text-center shadow-romantic sm:p-12">
            <div class="mx-auto flex h-20 w-20 items-center justify-center rounded-full bg-love-primary/10 text-love-primary animate-float">
                <?php echo renderIcon('heart-broken', 'h-10 w-10'); ?>
            </div>
            <p class="mt-6 inline-flex items-center gap-2 rounded-full border border-love-primary/15 bg-love-primary/5 px-4 py-2 text-sm font-semibold text-love-dark">
                <?php echo renderIcon('sparkles', 'h-4 w-4'); ?>
                链接可能已失效或内容已被移除
            </p>
            <h1 class="mt-6 text-3xl font-extrabold tracking-tight text-slate-900 sm:text-4xl">抱歉，找不到这份告白</h1>
            <p class="mt-4 text-base leading-7 text-slate-600 sm:text-lg">你访问的内容可能不存在、已过期，或者分享链接输入有误。可以返回首页重新生成一份新的浪漫告白。</p>
            <div class="mt-8 flex flex-col items-center justify-center gap-3 sm:flex-row">
                <a href="/index.html" class="ring-focus inline-flex min-h-12 items-center justify-center gap-2 rounded-full bg-love-primary px-6 py-3 text-sm font-semibold text-white shadow-romantic transition-colors duration-200 hover:bg-love-dark cursor-pointer">
                    <?php echo renderIcon('home', 'h-4 w-4'); ?>
                    返回首页
                </a>
                <button type="button" onclick="history.back()" class="ring-focus inline-flex min-h-12 items-center justify-center gap-2 rounded-full border border-slate-200 bg-white px-6 py-3 text-sm font-semibold text-slate-700 shadow-soft transition-colors duration-200 hover:border-love-primary/40 hover:text-love-dark cursor-pointer">
                    <?php echo renderIcon('arrow-left', 'h-4 w-4'); ?>
                    返回上一页
                </button>
            </div>
        </section>
    </main>
    <?php
    renderAppShellEnd();
    exit;
}

$passwordVerified = empty($confession['password']);
$errorMessage = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['password'])) {
    $submittedPassword = trim((string) $_POST['password']);
    if ($submittedPassword !== (string) ($confession['password'] ?? '')) {
        $errorMessage = '密码错误，请重新输入。';
    } else {
        $passwordVerified = true;
    }
}

if (!$passwordVerified) {
    renderAppShellStart('请输入密码 - 爱在指尖');
    ?>
    <main class="relative isolate flex min-h-screen items-center justify-center px-4 py-10 sm:px-6 lg:px-8">
        <div class="hero-orb left-[2%] top-[10%] h-40 w-40 bg-love-primary/40"></div>
        <div class="hero-orb bottom-[10%] right-[4%] h-52 w-52 bg-rose-200/45"></div>
        <section class="glass-panel relative z-10 w-full max-w-lg overflow-hidden rounded-[2rem] shadow-romantic">
            <div class="border-b border-white/70 bg-[linear-gradient(135deg,rgba(255,75,145,0.92),rgba(255,133,179,0.88))] px-7 py-8 text-white sm:px-10">
                <div class="flex h-16 w-16 items-center justify-center rounded-full bg-white/15 text-white shadow-lg">
                    <?php echo renderIcon('lock', 'h-8 w-8'); ?>
                </div>
                <h1 class="mt-5 text-3xl font-extrabold tracking-tight">这份告白已加密</h1>
                <p class="mt-3 max-w-md text-sm leading-6 text-white/85">发送者为这封信设置了查看密码。输入正确密码后，即可进入专属告白页面。</p>
            </div>
            <div class="px-7 py-8 sm:px-10 sm:py-10">
                <form method="post" id="passwordForm" class="space-y-5" novalidate>
                    <?php if ($errorMessage !== ''): ?>
                    <div id="passwordError" class="animate-[shake_.45s_cubic-bezier(.36,.07,.19,.97)_both] rounded-2xl border border-rose-200 bg-rose-50 px-4 py-3 text-sm font-medium text-rose-700" role="alert">
                        <?php echo htmlspecialchars($errorMessage, ENT_QUOTES, 'UTF-8'); ?>
                    </div>
                    <?php endif; ?>
                    <div class="space-y-2">
                        <label for="password" class="text-sm font-semibold text-slate-800">查看密码</label>
                        <div class="relative">
                            <span class="pointer-events-none absolute inset-y-0 left-0 flex items-center pl-4 text-slate-400">
                                <?php echo renderIcon('key', 'h-5 w-5'); ?>
                            </span>
                            <input
                                type="password"
                                id="password"
                                name="password"
                                required
                                autocomplete="off"
                                aria-describedby="passwordHint<?php echo $errorMessage !== '' ? ' passwordError' : ''; ?>"
                                placeholder="请输入发送者提供的密码"
                                value="<?php echo isset($_POST['password']) ? htmlspecialchars((string) $_POST['password'], ENT_QUOTES, 'UTF-8') : ''; ?>"
                                class="ring-focus min-h-12 w-full rounded-2xl border border-slate-200 bg-white py-3 pl-12 pr-4 text-slate-900 placeholder:text-slate-400 transition-colors duration-200 focus:border-love-primary">
                        </div>
                        <p id="passwordHint" class="text-sm text-slate-500">密码区分大小写，建议与发送者确认后再尝试。</p>
                    </div>
                    <button type="submit" class="ring-focus inline-flex min-h-12 w-full items-center justify-center gap-2 rounded-2xl bg-love-primary px-6 py-3 text-sm font-semibold text-white shadow-romantic transition-colors duration-200 hover:bg-love-dark cursor-pointer">
                        <?php echo renderIcon('unlock', 'h-5 w-5'); ?>
                        解锁告白
                    </button>
                </form>
                <div class="mt-6 flex items-center justify-center">
                    <a href="/index.html" class="ring-focus inline-flex items-center gap-2 text-sm font-semibold text-love-dark transition-colors duration-200 hover:text-love-primary cursor-pointer">
                        <?php echo renderIcon('arrow-left', 'h-4 w-4'); ?>
                        返回首页
                    </a>
                </div>
            </div>
        </section>
    </main>
    <?php
    renderAppShellEnd();
    exit;
}

$bgColors = [
    'love-primary' => '#FF4B91',
    'love-secondary' => '#FF85B3',
    'love-accent' => '#FFC1D6',
    'love-dark' => '#C2185B'
];

$bgColorKey = (string) ($confession['bgColor'] ?? 'love-primary');
$bgColor = $bgColors[$bgColorKey] ?? $bgColors['love-primary'];
$bgColorLight = hexToLightenColor($bgColor, 0.9);
$displayDate = !empty($confession['timestamp']) ? date('Y年m月d日', strtotime((string) $confession['timestamp'])) : date('Y年m月d日');
$pageTitle = sprintf('%s，来自%s的一封信 - 爱在指尖', (string) ($confession['recipient'] ?? '亲爱的'), (string) ($confession['sender'] ?? '匿名'));
$messageHtml = nl2br(htmlspecialchars((string) ($confession['message'] ?? ''), ENT_QUOTES, 'UTF-8'));
$templateInfo = loadTemplateConfig($confession['template'] ?? 'default');
$templateConfig = $templateInfo['config'];
$templateHtml = loadTemplateHtml($templateInfo['templateName']);
$currentUrl = buildCurrentUrl();

$pageVariables = [
    'pageTitle' => htmlspecialchars($pageTitle, ENT_QUOTES, 'UTF-8'),
    'recipient' => htmlspecialchars((string) ($confession['recipient'] ?? '亲爱的'), ENT_QUOTES, 'UTF-8'),
    'sender' => htmlspecialchars((string) ($confession['sender'] ?? '匿名'), ENT_QUOTES, 'UTF-8'),
    'messageHtml' => $messageHtml,
    'displayDate' => htmlspecialchars($displayDate, ENT_QUOTES, 'UTF-8'),
    'bgColor' => htmlspecialchars($bgColor, ENT_QUOTES, 'UTF-8'),
    'bgColorLight' => htmlspecialchars($bgColorLight, ENT_QUOTES, 'UTF-8'),
    'currentUrl' => htmlspecialchars($currentUrl, ENT_QUOTES, 'UTF-8'),
    'shareTitle' => htmlspecialchars((string) ($templateConfig['shareTitle'] ?? '继续分享这份爱意'), ENT_QUOTES, 'UTF-8'),
    'shareDescription' => htmlspecialchars((string) ($templateConfig['shareDescription'] ?? '复制当前链接，或通过常用渠道把这封信继续传递给对方与朋友。'), ENT_QUOTES, 'UTF-8'),
    'heroEyebrow' => htmlspecialchars((string) ($templateConfig['heroEyebrow'] ?? '专属告白已送达'), ENT_QUOTES, 'UTF-8'),
    'letterIntro' => htmlspecialchars((string) ($templateConfig['letterIntro'] ?? '每一句认真书写的话，都值得被郑重地阅读、保存和分享。'), ENT_QUOTES, 'UTF-8'),
    'securityTitle' => htmlspecialchars((string) ($templateConfig['securityTitle'] ?? '安全查看说明'), ENT_QUOTES, 'UTF-8'),
    'musicButtonText' => htmlspecialchars((string) ($templateConfig['musicButtonText'] ?? '播放音乐'), ENT_QUOTES, 'UTF-8'),
    'musicHint' => htmlspecialchars((string) ($templateConfig['musicHint'] ?? '如果浏览器阻止自动播放，可以点击上方按钮手动开启或暂停背景音乐。'), ENT_QUOTES, 'UTF-8'),
    'musicUrl' => htmlspecialchars((string) ($templateConfig['musicUrl'] ?? ''), ENT_QUOTES, 'UTF-8'),
    'iconHeart' => renderIcon('heart', 'h-5 w-5'),
    'iconArrowLeft' => renderIcon('arrow-left', 'h-4 w-4'),
    'iconSparkles' => renderIcon('sparkles', 'h-4 w-4'),
    'iconCalendar' => renderIcon('calendar', 'h-4 w-4 text-love-primary'),
    'iconQuoteLeft' => renderIcon('quote-left', 'h-10 w-10'),
    'iconQuoteRight' => renderIcon('quote-right', 'h-10 w-10'),
    'iconCopy' => renderIcon('copy', 'h-4 w-4'),
    'iconWeibo' => renderIcon('weibo', 'h-5 w-5'),
    'iconWechat' => renderIcon('wechat', 'h-5 w-5'),
    'iconQQ' => renderIcon('qq', 'h-5 w-5'),
    'iconEmail' => renderIcon('email', 'h-5 w-5'),
    'iconShieldCheck' => renderIcon('shield-check', 'h-4 w-4'),
    'iconMusicNote' => renderIcon('music-note', 'h-5 w-5')
];

renderAppShellStart($pageTitle);
echo renderTemplateHtml($templateHtml, $pageVariables);
renderAppShellEnd();
