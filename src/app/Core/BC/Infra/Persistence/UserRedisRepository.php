#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\UserRepository;
use Kernel\Domain\AggregateRootInterface;
use Kernel\Domain\ValueObjects\Identity;
use Kernel\System\Clients\RedisClient;
use Redis;

final class UserRedisRepository // implements UserRepository
{
    private Redis $redis;
    private $client;

    public function __construct()
    {
        $this->client = RedisClient::getInstance();
        $this->redis = $this->client->getConn();
    }

    public function append(AggregateRootInterface $aggregate): void
    {
        var_dump($aggregate);
        logger($aggregate->toArray());
        // $this->redis->set('user' . $aggregate->id(), json_encode($aggregate->toArray()));
        // $this->client->putConn($this->redis);
    }

    public function getEventsFor(Identity $aggregateId): iterable
    {
        return [];
    }
}
