#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Shared\Providers\JobProvider;
use Bootstrap\Application;
use Kernel\System\Jobs\JobScheduler;
use Kernel\System\Server\RunServer;
use Swoole\Runtime;

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

// Enable Hooks to allow Courotines to work automatically
// https://openswoole.com/docs/modules/swoole-runtime-flags
!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', \SWOOLE_HOOK_ALL);
Runtime::enableCoroutine(true, \SWOOLE_HOOK_ALL);

require_once __DIR__  . '/../kernel/Helpers/functions.php';

Application::getInstance();

// Job Scheduling
$jobScheduler = new JobScheduler(new JobProvider());

/**
 *  Start Server and Handlers.
 *  Pass needed server class as parameter
 */
$server = new RunServer(
    server: \Swoole\Http\Server::class,
    processes: [$jobScheduler]
);
