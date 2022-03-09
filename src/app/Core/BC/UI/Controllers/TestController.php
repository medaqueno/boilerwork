#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

use Swoole\Http\Request;
use App\Core\Mailer;
use Kernel\Clients\RedisClient;

final class TestController
{
    public function __construct(public Mailer $mailer)
    {
    }

    public function __invoke(Request $request, array $vars): array
    {
        $this->redisPool = RedisClient::getInstance();
        $redis = $this->redisPool->getConn();
        $redis->set('SetOneKey', 'some value set' . rand(0, 999999));
        $valor = $redis->get('SetOneKey');
        $this->redisPool->putConn($redis);  // Connection should be released
        return [
            'message' => 'Invoke method',
            'data' => [
                // 'fromDependencyInjection' => $this->mailer->getTestString(),
                'fromRedis' => $valor,
                // 'fromDependencyInjection2' => $this->response->response(),
                'request' => $request->get['parameter1'] ?? null,
                'vars' => $vars
            ]

        ];
    }

    public function customMethod(Request $request, array $vars)
    {
        $result =  [
            'message' => 'customMethod',
            'data' => [
                'fromDependencyInjection' => $this->mailer->getTestString(),
                // 'fromDependencyInjection2' => $this->response->response(),
                'request' => $request->get ?? null,
                'vars' => $vars
            ]
        ];

        return $result;
    }
}
