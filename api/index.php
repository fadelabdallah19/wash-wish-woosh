<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Bootstrap path — Vercel runs from /api/, Laravel root is one level up
$basePath = dirname(__DIR__);

// Maintenance mode
if (file_exists($maintenance = $basePath . '/storage/framework/maintenance.php')) {
    require $maintenance;
}

// Composer autoloader
require $basePath . '/vendor/autoload.php';

// Bootstrap Laravel
$app = require_once $basePath . '/bootstrap/app.php';

$app->handleRequest(Request::capture());
