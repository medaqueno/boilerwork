#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use Kernel\Domain\RecordsEvents;
use Kernel\Domain\ValueObjects\Identity;
use Kernel\Infra\Persistence\EventStore;

final class UserRepository
{
    private $eventStore;

    public function __construct(EventStore $eventStore)
    {
        $this->eventStore = $eventStore;
    }

    public function add(RecordsEvents $aggregate): void
    {
        $events = $aggregate->getRecordedEvents();
        $this->eventStore->commit($events);
        $aggregate->clearRecordedEvents();
    }

    public function get(Identity $aggregateId)
    {
        $aggregateHistory = $this->eventStore->getAggregateHistoryFor($aggregateId);

        return User::reconstituteFrom($aggregateHistory);
    }
}
