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
            $this->events[] = [
                $event->getAggregateId(),
                json_encode($event->serialize()),
                $aggregate->currentVersion()
            ];
        }

        $aggregate->clearRecordedEvents();
    }

    /**
     *  @inheritDoc
     **/
    public function getAggregateHistoryFor(Identity $aggregateId): User
    {
        // Filter events by aggregateID And map them to be reconstituted
        $mappedEvents = array_map(
            function (array $event) {
                return json_decode($event[1], true);
            },
            array_filter( // Retrieve events by aggregateId. Same as select <fields> where aggregateId = <aggregateId>;
                $this->events,
                function (array $event) use ($aggregateId) {
                    return $event[0] === $aggregateId->toPrimitive();
                }
            )
        );

        return User::reconstituteFrom(
            new AggregateHistory(
                $aggregateId,
                $mappedEvents
            )
        );
    }
}
