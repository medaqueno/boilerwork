#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\Domain\RecordsEvents;
use Kernel\Infra\Persistence\EventStore;

final class UserRedisRepository implements UserRepository
{
    /**
     *  Inject Client Repository in Infrastructure by its interface
     **/
    public function __construct(private EventStore $eventStore)
    {
    }

    /**
     *  @inheritDoc
     **/
    public function add(RecordsEvents $aggregate): void
    {
        $events = $aggregate->getRecordedEvents();
        $this->eventStore->append($events);
        $aggregate->clearRecordedEvents();
    }

    /**
     *  @inheritDoc
     **/
    public function get($aggregateId): RecordsEvents
    {
        return User::reconstituteFrom(
            $this->eventStore->getAggregateHistoryFor($aggregateId)
        );
    }
}
