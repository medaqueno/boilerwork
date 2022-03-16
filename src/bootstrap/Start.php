#!/usr/bin/env php
<?php

declare(strict_types=1);

use Bootstrap\Application;

define('APP_PATH', __DIR__ . '/../app');
define('BASE_PATH', __DIR__ . '/..');

/**
 * Dotenv initialization
 **/
if (file_exists(__DIR__ . '/../.env') === true) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

error_reporting(E_ALL);
ini_set('display_errors', $_ENV['APP_DEBUG'] ?? false);

/**
 * Set Timezone
 **/
date_default_timezone_set($_ENV['APP_TIMEZONE']);

require_once __DIR__  . '/../kernel/Helpers/functions.php';

Application::getInstance();

new \Kernel\System\RunServer('http');
