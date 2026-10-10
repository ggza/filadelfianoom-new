<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Ha a projekt gyökerében lévő .htaccess irányította ide a kérést (a címben nincs "/public"),
// a Laravel a projekt mappáját tekintse alapcímnek, különben almappában minden útvonal 404 lenne.
$script = $_SERVER['SCRIPT_NAME'] ?? '';
if (str_ends_with($script, '/public/index.php')) {
    $base = substr($script, 0, -strlen('/public/index.php'));
    $uri = strtok($_SERVER['REQUEST_URI'] ?? '', '?');
    if ($uri !== $base.'/public' && ! str_starts_with($uri, $base.'/public/')) {
        $_SERVER['SCRIPT_NAME'] = $_SERVER['PHP_SELF'] = $base.'/index.php';
    }
}

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
