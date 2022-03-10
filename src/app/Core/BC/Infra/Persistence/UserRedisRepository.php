#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\System\Clients\RedisClient;
use Redis;

final class UserRedisRepository implements UserRepository
{
    private Redis $redis;
    private $client;

    public function __construct()
    {
        $this->client = RedisClient::getInstance();
        $this->redis = $this->client->getConn();
    }

    public function add(User $user): void
    {
        $this->redis->set('user' . $user->id(), json_encode($user->toArray()));
        $this->client->putConn($this->redis);
    }

    public function remove(User $user): void
    {
    }

    public function ofId(int $userId): mixed
    {
        $response = $this->redis->get('user' . $userId);
        $this->client->putConn($this->redis);

        return $response;
    }
}
