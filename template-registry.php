<?php

function scanTemplates() {
    $templatesDir = __DIR__ . '/templates';
    if (!is_dir($templatesDir)) {
        return [];
    }

    $entries = scandir($templatesDir);
    if ($entries === false) {
        return [];
    }

    $templates = [];
    foreach ($entries as $entry) {
        if ($entry === '.' || $entry === '..') {
            continue;
        }

        $templateDir = $templatesDir . '/' . $entry;
        if (!is_dir($templateDir)) {
            continue;
        }

        $configPath = $templateDir . '/config.php';
        $templatePath = $templateDir . '/template.html';
        if (!is_file($configPath) || !is_file($templatePath)) {
            continue;
        }

        $config = require $configPath;
        if (!is_array($config)) {
            continue;
        }

        $templates[] = [
            'key' => $entry,
            'name' => (string) ($config['name'] ?? $entry),
            'description' => (string) ($config['description'] ?? ''),
            'previewText' => (string) ($config['previewText'] ?? ''),
            'isDefault' => !empty($config['isDefault']),
            'musicUrl' => (string) ($config['musicUrl'] ?? ''),
            'shareTitle' => (string) ($config['shareTitle'] ?? ''),
            'shareDescription' => (string) ($config['shareDescription'] ?? ''),
            'heroEyebrow' => (string) ($config['heroEyebrow'] ?? ''),
            'letterIntro' => (string) ($config['letterIntro'] ?? ''),
            'securityTitle' => (string) ($config['securityTitle'] ?? ''),
            'musicButtonText' => (string) ($config['musicButtonText'] ?? ''),
            'musicHint' => (string) ($config['musicHint'] ?? ''),
            'templatePath' => $templatePath,
            'configPath' => $configPath,
        ];
    }

    usort($templates, function ($left, $right) {
        if ($left['isDefault'] === $right['isDefault']) {
            return strcmp($left['key'], $right['key']);
        }
        return $left['isDefault'] ? -1 : 1;
    });

    return $templates;
}

function getDefaultTemplate(array $templates) {
    foreach ($templates as $template) {
        if (!empty($template['isDefault'])) {
            return $template;
        }
    }

    foreach ($templates as $template) {
        if (($template['key'] ?? '') === 'default') {
            return $template;
        }
    }

    return $templates[0] ?? null;
}

function getTemplateMap(array $templates) {
    $map = [];
    foreach ($templates as $template) {
        $map[$template['key']] = $template;
    }
    return $map;
}

function resolveTemplate($templateKey) {
    $templates = scanTemplates();
    $templateMap = getTemplateMap($templates);
    $templateKey = trim((string) $templateKey);

    if ($templateKey !== '' && isset($templateMap[$templateKey])) {
        return $templateMap[$templateKey];
    }

    return getDefaultTemplate($templates);
}
