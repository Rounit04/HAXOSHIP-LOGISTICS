<?php

use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

define('LARAVEL_START', microtime(true));

// Increase PHP upload limits early (before Laravel loads)
// Note: upload_max_filesize and post_max_size can only be set in php.ini,
// but we try to set other limits here
@ini_set('memory_limit', '1024M');
@ini_set('max_execution_time', '1800');
@ini_set('max_input_time', '1800');

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
