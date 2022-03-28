#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\Domain\AggregateHistory;
use Kernel\Domain\RecordsEvents;
use Kernel\Domain\ValueObjects\Identity;

final class UserInMemoryRepository implements UserRepository
{
    /**
     *  Store events in memory
     **/
    private array $events = [];

    /**
     *  @inheritDoc
     **/
    public function append(RecordsEvents $aggregate): void
    {
        $events = $aggregate->getRecordedEvents();

        foreach ($events as $event) {
            $this->events[] = $event->serialize();
        }

        $aggregate->clearRecordedEvents();
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
