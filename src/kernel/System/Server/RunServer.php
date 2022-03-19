#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Server;

final class RunServer
{
    // private \Swoole\Server $server;

    public function __construct(
        private $server = \Swoole\Server::class,
        private array $processes = []
    ) {

        $this->server = new $server($_ENV['SERVER_IP'], intval($_ENV['SERVER_PORT']));

        // https://openswoole.com/docs/modules/swoole-server/configuration
        $this->server->set([
            'worker_num' => swoole_cpu_num() * 2,
            'task_worker_num' => swoole_cpu_num(),
            'task_enable_coroutine' => true,
            // 'enable_preemptive_scheduler' => 1,
            // 'dispatch_mode' => 3, // in preemptive mode, the main process will select delivery according to the worker's free and busy state, and will only deliver to the worker in idle state
            // 'max_conn' => CONFIGURE IF NEEDED AS DOCS RECOMMENDS,
            'open_http2_protocol' => true,
            'debug_mode' => boolval($_ENV['APP_DEBUG']),
            'log_level' => boolval($_ENV['APP_DEBUG']) ? 0 : 5,
            'log_file' => base_path('/logs/swoole_http_server.log'),
            'log_rotation' => SWOOLE_LOG_ROTATION_DAILY,
            'log_date_format' => '%Y-%m-%dT%H:%M:%S%z',
        ]);

        // Set server event handlers

        $this->server->on(
            "start",
            [$this, 'onStart']
        );

        $handleTcpUdp = new HandleTcpUdp;
        $this->server->on(
            "receive",
            [$handleTcpUdp, 'onReceive']
        );

        $handleWorkers = new HandleWorkers;
        $this->server->on(
            "WorkerStart",
            [$handleWorkers, 'onWorkerStart']
        );
        $this->server->on(
            "WorkerStop",
            [$handleWorkers, 'onWorkerStop']
        );

        $handleTasks = new HandleTasks;
        $this->server->on(
            "task",
            [$handleTasks, 'onTask']
        );

        // Websocket server may receive requests too, we leave option to
        // comment out and allow it (take care of control the right requests to each server)
        $handleWebSocket = null;
        if ($this->server instanceof \Swoole\WebSocket\Server) {
            $handleWebSocket = new HandleWebSocket;
            $this->server->on(
                "Open",
                [$handleWebSocket, 'onOpen']
            );

            $this->server->on(
                "Message",
                [$handleWebSocket, 'onMessage']
            );

            $this->server->on(
                "Close",
                [$handleWebSocket, 'onClose']
            );
        }

        if ($this->server instanceof \Swoole\Http\Server) {
            $handleHttp = new HandleHttp;
            $this->server->on(
                "request",
                function (\Swoole\Http\Request $request, \Swoole\Http\Response $response) use ($handleHttp, $handleWebSocket) {
                    $handleHttp->onRequest(request: $request, response: $response);

                    /* // We may allow to send requests to Websocket server if it is needed
                    if ($this->server instanceof \Swoole\WebSocket\Server) {
                        $handleWebSocket->onRequest(request: $request, response: $response);
                    }*/
                }
            );
        }

        // Add dedicated processes to Server Event Loop
        if (count($processes) > 0) {
            foreach ($processes as $process) {
                $this->server->addProcess($process->process());
            }
        }

        $this->server->start();
    }

    public function onStart(\Swoole\Server $server): void
    {
        echo "\nSERVER STARTED: " . get_class($server) . "\n";
        swoole_set_process_name('swoole_server');
    }

    public function getServer(): mixed
    {
        return $this->server;
    }
}
