<?php

require_once dirname(__DIR__) . '/vendor/autoload.php';

use Dotenv\Dotenv;

if (!defined('APP_ENV_LOADED')) {
    $dotenv = Dotenv::createImmutable(dirname(__DIR__));
    $dotenv->safeLoad();
    define('APP_ENV_LOADED', true);
}

function env(string $key, $default = null)
{
    $value = $_ENV[$key] ?? $_SERVER[$key] ?? getenv($key);

    if ($value === false || $value === null || $value === '') {
        return $default;
    }

    $normalized = strtolower((string) $value);

    if ($normalized === 'true') {
        return true;
    }

    if ($normalized === 'false') {
        return false;
    }

    if ($normalized === 'null') {
        return null;
    }

    return $value;
}
