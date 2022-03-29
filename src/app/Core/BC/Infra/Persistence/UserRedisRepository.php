#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\Domain\AggregateHistory;
use Kernel\Domain\RecordsEvents;
use Kernel\Domain\ValueObjects\Identity;
use Kernel\System\Clients\RedisClient;
use Redis;

final class UserRedisRepository implements UserRepository
{
    private Redis $redis;

    public function __construct()
    {
        $this->redisPool = RedisClient::getInstance();
        $this->redis = $this->redisPool->getConn();
    }

    /**
     *  @inheritDoc
     **/
    public function append(RecordsEvents $aggregate): void
    {
        $events = $aggregate->getRecordedEvents();

        foreach ($events as $event) {
            // $this->redis->hMSet($aggregate::class . ':' . $event->getAggregateId(), $event->serialize());
            // $this->redis->xAdd($event::class, $event->getAggregateId(), $event->serialize());
        }

        $aggregate->clearRecordedEvents();

        $this->redisPool->putConn($this->redis);
    }

    /**
     *  @inheritDoc
     **/
    public function getAggregateHistoryFor(Identity $aggregateId): RecordsEvents
    {
        return User::reconstituteFrom(
            new AggregateHistory(
                $aggregateId,
                array_filter( // Retrieve events by aggregateId. Same as select <fields> where aggregateId = <aggregateId>;
                    $this->events,
                    function (array $event) use ($aggregateId) {
                        return $event['aggregateId'] === $aggregateId->toPrimitive();
                    }
                )
            )
        );
    }
}
