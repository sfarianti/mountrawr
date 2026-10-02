<?php

declare(strict_types=1);

define('BASE_PATH', __DIR__);

// Load custom Env handler
require_once BASE_PATH . '/app/Core/Env.php';
\App\Core\Env::load(BASE_PATH . '/.env');

// Set timezone
date_default_timezone_set(getenv('TIMEZONE') ?: 'Asia/Jakarta');

// PSR-4 Autoloader for App\ namespace
spl_autoload_register(function ($class) {
    $prefix = 'App\\';
    $base_dir = BASE_PATH . '/app/';

    $len = strlen($prefix);
    if (strncmp($prefix, $class, $len) !== 0) {
        return;
    }

    $relative_class = substr($class, $len);
    $file = $base_dir . str_replace('\\', '/', $relative_class) . '.php';

    if (file_exists($file)) {
        require_once $file;
    }
});

// Load global helpers
require_once BASE_PATH . '/app/helpers.php';

// Setup error logging
if (getenv('APP_DEBUG') === 'true') {
    ini_set('display_errors', '1');
    error_reporting(E_ALL);
} else {
    ini_set('display_errors', '0');
    error_reporting(0);
}

ini_set('log_errors', '1');
ini_set('error_log', BASE_PATH . '/storage/logs/app.log');
