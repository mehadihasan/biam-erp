<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Local dev environments on Windows can be slow (AV / filesystem / cold caches),
// which can cause UI routes (e.g. Filament) to hit the default 30s limit.
// If you prefer, set this in php.ini instead.
if (function_exists('set_time_limit')) {
    @set_time_limit(120);
}
@ini_set('max_execution_time', '120');

// Determine if the application is in maintenance mode...
if (file_exists($maintenance = __DIR__.'/../storage/framework/maintenance.php')) {
    require $maintenance;
}

// Register the Composer autoloader...
require __DIR__.'/../vendor/autoload.php';

// Bootstrap Laravel and handle the request...
/** @var Application $app */
$app = require_once __DIR__.'/../bootstrap/app.php';

$app->handleRequest(Request::capture());
