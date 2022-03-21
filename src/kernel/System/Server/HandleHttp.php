#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Server;

use Kernel\System\Http\Request as HttpRequest;
use Psr\Http\Message\ResponseInterface;
use Swoole\Http\Request;
use Swoole\Http\Response;

/**
 *
 **/
final class HandleHttp
{
    private \FastRoute\Dispatcher $httpDispatcher;

    public function __construct()
    {
        // Init Routing
        $this->httpDispatcher = $this->initRouting();
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
        // Rate Limit -> It should be a middleware
        /*
        $Ratelimiter = RateLimiter::getInstance();
        $count = $Ratelimiter->access($request->server['remote_addr']);
        if ($count > $Ratelimiter::MAX_REQUESTS) {
            $response->setStatusCode(429);
            $response->header("Content-Type", "text/plain");
            $response->end("Blocked");
            return;
        }
        */

        try {
            $result = $this->handleRequest($request);
        } catch (\Throwable $e) {
            error($e->getMessage());
            // // https://jsonapi.org/examples/#error-objects
            $response->setStatusCode($e->getCode());
            $result = json_encode([
                "errors" => [
                    [
                        "status" => $e->getCode(),
                        // "code" => "226",
                        // "source" => ["pointer" => "/data/attributes/firstName"],
                        "title" =>  $e->getMessage(),
                        "detail" => ""
                    ]
                ]
            ]);
        }

        foreach ($result->getHeaders() as $key => $value) {
            $response->setHeader($key, $value[0]);
        }

        $response->setStatusCode($result->getStatusCode(), $result->getReasonPhrase());
        $response->end($result->getBody()->__toString());

        // go(function () {
        //     getMemoryStatus();
        // });
    }

    private function handleRequest(Request $swooleRequest): ResponseInterface
    {
        // Convert Swoole Request to Psr\Http\Message\ServerRequestInterface
        $request = HttpRequest::createFromSwoole($swooleRequest);

        $request_method = $request->getMethod();
        $request_uri = $request->getServerParams()['request_uri'];

        $dispatched = $this->httpDispatcher->dispatch($request_method, $request_uri);

        if (count($dispatched) === 1) {
            return null;
        }

        $code = $dispatched[0];
        $handler = $dispatched[1];
        $vars = $dispatched[2];

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
                    $class = app()->container()->get($className);
                    $result = $class->$method($request, $vars);
                } else {
                    // invokable class  __invoke
                    $result = (app()->container()->get($handler))($request, $vars);
                    // $result = (new $handler)($request, $vars);
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