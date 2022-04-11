#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Clients;

use Redis;

/**
 * Open a pool of connections to Redis server, so we can use them when needed.
 *
 * @example []
        $redisPool = RedisClient::getInstance();
        $redis = $redisPool->getConn();
        $redis->set('SetOneKey', 'some value set');
        var_dump($redis->get('SetOneKey'));
        $redisPool->putConn($redis);  // Connection must be released
 **/
final class RedisClient
{
    public readonly Redis $conn;

    private readonly RedisPool $pool;

    public function __construct()
    {
        $this->pool = RedisPool::getInstance();
        $this->conn = $this->pool->getConn();
    }

    public function hGet($key, $hashKey): string
    {
        return $this->conn->hGet($key, $hashKey);
    }

    public function hGetAll($key): array
    {
        return $this->conn->hGetAll($key);
    }

    public function hSet($key, $hashKey, $value): int|bool
    {
        return $this->conn->hSet($key, $hashKey, $value);
    }

    /**
     * Put connection back to the pool in order to be reused
     **/
    public function putConnection(Redis $conn): void
    {
        $this->pool->putConn($conn);
    }

    public function initTransaction(): Redis
    {
        return $this->conn->multi();
    }

    public function endTransaction()
    {
        return $this->conn->exec();
    }
}
