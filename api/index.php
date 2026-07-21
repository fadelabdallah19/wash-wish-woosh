<?php

use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

$basePath = dirname(__DIR__);
$tmpPath = '/tmp';

// Create writable directories on cold start
$dirs = [
    $tmpPath . '/storage/framework/views',
    $tmpPath . '/storage/framework/cache',
    $tmpPath . '/storage/framework/cache/data',
    $tmpPath . '/storage/framework/sessions',
    $tmpPath . '/storage/logs',
    $basePath . '/bootstrap/cache',
];
foreach ($dirs as $dir) {
    if (!is_dir($dir)) {
        mkdir($dir, 0755, true);
    }
}

// Maintenance mode
if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $basePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
