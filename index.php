<?php

$appBase = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? ''));
$appBase = rtrim($appBase, '/');

if ($appBase === '.' || $appBase === '/') {
    $appBase = '';
}

function app_url(string $page = 'dashboard'): string
{
    $base = $GLOBALS['appBase'] ?? '';

    return $base . '/index.php?page=' . rawurlencode($page);
}

function asset_url(string $path): string
{
    $base = $GLOBALS['appBase'] ?? '';

    return $base . '/' . ltrim($path, '/');
}

function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES, 'UTF-8');
}

$page = $_GET['page'] ?? 'dashboard';
$pages = [
    'dashboard' => __DIR__ . '/View/dashboard.php',
    'buscar' => __DIR__ . '/View/buscar.php',
];

if (!isset($pages[$page])) {
    $page = 'dashboard';
}

$viewFile = $pages[$page];
$pageTitle = $page === 'buscar' ? 'Nueva búsqueda' : 'Panel';

require __DIR__ . '/View/layout.php';
