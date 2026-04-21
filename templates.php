<?php
header('Content-Type: application/json; charset=utf-8');

require_once __DIR__ . '/template-registry.php';

$templates = scanTemplates();
$response = array_map(function ($template) {
    return [
        'key' => $template['key'],
        'name' => $template['name'],
        'description' => $template['description'],
        'previewText' => $template['previewText'],
        'isDefault' => $template['isDefault'],
    ];
}, $templates);

echo json_encode([
    'success' => true,
    'templates' => $response,
], JSON_UNESCAPED_UNICODE);
