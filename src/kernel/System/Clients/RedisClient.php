#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Clients;

use Kernel\Helpers\Singleton;
use Redis;
use Swoole\Database\RedisConfig;
use Swoole\Database\RedisPool;

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
    use Singleton;

    private RedisPool $pool;

    protected function __construct()
    {
        if (boolval($_ENV['REDIS_ENABLED']) === false) {
            throw new \Exception("REDIS IS NOT ENABLED", 500);

            return;
        }

        // \Swoole\Coroutine::set(['hook_flags' => SWOOLE_HOOK_TCP]); // Enabled Globally in Application init

        $this->pool = new RedisPool((new RedisConfig)
                ->withHost($_ENV['REDIS_HOST'])
                ->withPort((int)$_ENV['REDIS_PORT'])
                ->withAuth('')
                // ->withDbIndex(0)
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

    /* public function save(mixed $object)
    {
        $this->client->hset('posts', (string) $object->id(), serialize($object));
    }
    public function remove(Post $aPost)
    {
        $this->client->hdel('posts', (string) $aPost->id());
    }
    public function get(PostId $anId)
    {
        if ($data = $this->client->hget('posts', (string) $anId)) {
            return unserialize($data);
        }
        return null;
    }*/
}
