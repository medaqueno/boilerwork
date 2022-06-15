#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Shared\Providers\JobsProvider;
use App\Shared\Providers\MessageProvider;
use Boilerwork\System\Container\Container;
use Bootstrap\Application;
use Boilerwork\System\Jobs\JobScheduler;
use Boilerwork\System\Message\MessageScheduler;
use Boilerwork\System\Server\RunServer;
use Swoole\Runtime;

/**
 * Dotenv initialization
 **/
if (file_exists(__DIR__ . '/../.env') === true) {
    $dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
    $dotenv->load();
}

// Enable Hooks to allow Courotines to work automatically
// https://openswoole.com/docs/modules/swoole-runtime-flags
!defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', \SWOOLE_HOOK_ALL);
Runtime::enableCoroutine(true, \SWOOLE_HOOK_ALL);

Application::getInstance();
Container::getInstance();

/**
 *  Start Server and Handlers.
 *  Pass needed server class as parameter
 */
$server = new RunServer(
    serverType: \Swoole\Http\Server::class,
    config: [
        'worker_num' => swoole_cpu_num() * 2,
        'task_worker_num' => swoole_cpu_num(),
        'task_enable_coroutine' => true,
        // 'enable_preemptive_scheduler' => 1,
        // 'dispatch_mode' => 3, // in preemptive mode, the main process will select delivery according to the worker's free and busy state, and will only deliver to the worker in idle state
        // 'max_conn' => CONFIGURE IF NEEDED AS DOCS RECOMMENDS,
        'open_mqtt_protocol' => true,
        'open_http2_protocol' => true,
        'debug_mode' => boolval($_ENV['APP_DEBUG']),
        'log_level' => boolval($_ENV['APP_DEBUG']) ? 0 : 5,
        'log_file' => base_path('/logs/swoole_http_server.log'),
        'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
        'log_date_format' => '%Y-%m-%dT%H:%M:%S%z',
    ],
    processes: [
        (new JobScheduler(new JobsProvider())), // Job Scheduling
        //(new MessageScheduler(new MessageProvider())) // Uncomment to enable Message Broker comms
    ],
);
