#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Infra\Persistence;

use Kernel\Domain\AggregateHistory;
use Kernel\Domain\ValueObjects\Identity;
use Kernel\System\Clients\RedisClient;
use Redis;

final class RedisEventStore implements EventStore
{
    private Redis $redis;
    private $client;

    public function __construct()
    {
        $this->client = RedisClient::getInstance();
        $this->redis = $this->client->getConn();
    }

    /**
     *  @inheritDoc
     **/
    public function append($events): void
    {
        foreach ($events as $event) {
            $this->redis->set($event->getAggregateId()->__toString(), json_encode($event));
        }
        $this->client->putConn($this->redis);
    }

    /**
     *  @inheritDoc
     **/
    public function getAggregateHistoryFor(Identity $id): AggregateHistory
    {
        var_dump($id);
        // Query events by ID
        $events = $this->redis->get($id->__toString());
        var_dump($events);
        return new AggregateHistory(
            $id,
            $events
        );
        $this->client->putConn($this->redis);
    }
}
