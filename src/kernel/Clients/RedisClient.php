#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Clients;

use Kernel\Helpers\Singleton;
use Redis;
use Swoole\Coroutine;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

/**
 * @example [] $redisPool = RedisClient::getInstance();
 *          $redis = $redisPool->getConn();
 *          $redis->set('SetOneKey', 'some value set');
 *          var_dump($redis->get('SetOneKey'));
 *          $redisPool->putConn($redis);  // Connection must be released
 **/
final class RedisClient
{
    use Singleton;

    private RedisPool $pool;

    public function __construct()
    {
        Coroutine::set(['hook_flags' => SWOOLE_HOOK_TCP]);

        $this->pool = new RedisPool((new RedisConfig)
                ->withHost($_ENV['REDIS_HOST'])
                ->withPort((int)$_ENV['REDIS_PORT'])
                ->withAuth('')
                // ->withDbIndex()
                ->withTimeout((int)1),
            $_ENV['REDIS_SIZE_CONN'] ? (int)$_ENV['REDIS_SIZE_CONN'] : 64
        );
    }

    public function getConn(): Redis
    {
        return $this->pool->get();
    }

    public function putConn(Redis $connection): void
    {
        $this->pool->put($connection);
    }
}
