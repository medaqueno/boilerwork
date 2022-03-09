#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Server;

use Kernel\Tasks\TaskScheduler;
use Swoole\Http\Request;
use Swoole\Http\Response;
use Swoole\Http\Server;

/**
 *
 **/
final class Http
{
    private \FastRoute\Dispatcher $httpDispatcher;

    public function __construct()
    {
        $server = new Server($_ENV['HTTP_SERVER_IP'], intval($_ENV['HTTP_SERVER_PORT']), SWOOLE_BASE);

        $server->set([
            'worker_num' => swoole_cpu_num() * 2,
            'task_worker_num' => swoole_cpu_num(),
            'task_enable_coroutine' => true,
            // 'enable_preemptive_scheduler' => 1,
            // 'dispatch_mode' => 3, // in preemptive mode, the main process will select delivery according to the worker's free and busy state, and will only deliver to the worker in idle state
            'open_http2_protocol' => true,
            'debug_mode' => 1,
            'log_level' => 0,
            'log_file' => base_path('/logs/swoole_http_server.log'),
            'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%d %H:%M:%S',
        ]);

        $server->on(
            "start",
            [$this, 'onStart']
        );

        // Init Routing
        $this->httpDispatcher = $this->initRouting();

        $server->on(
            "request",
            [$this, 'onRequest']
        );

        $server->on(
            "WorkerStart",
            [$this, 'onWorkerStart']
        );

        $server->on(
            "task",
            [$this, 'onTask']
        );

        $server->on(
            "WorkerStop",
            [$this, 'onWorkerStop']
        );

        $server->start();
    }

    public function onStart(Server $server): void
    {
        echo "\nHTTP SERVER STARTED\n";
        swoole_set_process_name('swoole_server');
    }

    public function onTask(Server $server, int $taskId, int $fromId, mixed $data): void
    {
        swoole_set_process_name('swoole_task_' . $taskId);
        echo  '\nTask start swoole_task_' . $taskId;
    }

    public function onWorkerStart(Server $server, int $workerId): void
    {
        swoole_set_process_name('swoole_worker_' . $workerId);
        // echo "\nWorker start swoole_worker_" . $workerId;

        if ($workerId === 0) {
            app()->containerBuilder->get(TaskScheduler::class);
        }
    }

    public function onWorkerStop(Server $server, int $workerId): void
    {
        echo "\nWorker Stop " . $workerId, "\n";
    }

    private function initRouting(): \FastRoute\Dispatcher
    {
        // Init Routing
        return \FastRoute\simpleDispatcher(
            function (\FastRoute\RouteCollector $r) {
                $routes = include(base_path('/routes/httpApi.php'));

                foreach ($routes as $route) {
                    $r->addRoute($route[0], $route[1], $route[2]);
                }
            }
        );
    }

    public function onRequest(Request $request, Response $response): void
    {
        // Rate Limit
        // $Ratelimiter = RateLimiter::getInstance();
        // $count = $Ratelimiter->access($request->server['remote_addr']);
        // if ($count > $Ratelimiter::MAX_REQUESTS) {
        //     $response->setStatusCode(429);
        //     $response->header("Content-Type", "text/plain");
        //     $response->end("Blocked");
        //     return;
        // }

        // populate the global state with the request info
        // $_SERVER['REQUEST_URI'] = $request->server['request_uri'];
        // $_SERVER['REQUEST_METHOD'] = $request->server['request_method'];
        // $_SERVER['REMOTE_ADDR'] = $request->server['remote_addr'];
        // $_GET = $request->get ?? [];
        // $_FILES = $request->files ?? [];
        // form-data and x-www-form-urlencoded work out of the box so we handle JSON POST here
        if ($request->server['request_method'] === 'POST' && $request->header['content-type'] === 'application/json') {
            $body = $request->rawContent();
            // $_POST = empty($body) ? [] : json_decode($body);
        } else {
            // $_POST = $request->post ?? [];
        }

        try {
            $result = $this->handleRequest($request);
        } catch (\Throwable $e) {

            // https://jsonapi.org/examples/#error-objects
            $response->setStatusCode($e->getCode());
            $result = [
                "errors" => [
                    [
                        "status" => $e->getCode(),
                        // "code" => "226",
                        // "source" => ["pointer" => "/data/attributes/firstName"],
                        "title" =>  $e->getMessage(),
                        "detail" => ""
                    ]
                ]
            ];
        }

        // global content type for our responses
        $response->header('Content-Type', 'application/json');

        // write the JSON string out
        $response->end(json_encode($result));
    }

    private function handleRequest(Request $request): mixed
    {
        $request_method = $request->server['request_method'];
        $request_uri = $request->server['request_uri'];

        list($code, $handler, $vars) = $this->httpDispatcher->dispatch($request_method, $request_uri);

        switch ($code) {
            case \FastRoute\Dispatcher::NOT_FOUND:
                $result = [
                    'message' => 'Not Found',
                    'errors' => [
                        sprintf('The URI "%s" was not found', $request_uri)
                    ]
                ];
                break;
            case \FastRoute\Dispatcher::METHOD_NOT_ALLOWED:

                $result = [
                    'message' => 'Method Not Allowed',
                    'errors' => [
                        sprintf('Method "%s" is not allowed', $request_method)
                    ]
                ];
                break;
            case \FastRoute\Dispatcher::FOUND:

                if (is_array($handler)) {
                    // Custom method in class
                    $className = $handler[0];
                    $method = $handler[1];
                    $class = app()->containerBuilder->get($className);
                    $result = $class->$method($request, $vars);
                } else {
                    // invokable class  __invoke
                    $result = (app()->containerBuilder->get($handler))($request, $vars);
                }

                break;
            default:
                $result = [
                    'message' => 'Server Error',
                    'errors' => [
                        sprintf('Server Error')
                    ]
                ];
        }

        return $result;
    }
}
