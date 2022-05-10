#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\Domain\AggregateHistory;
use Kernel\Domain\TracksEvents;
use Kernel\Domain\ValueObjects\Identity;

final class UserInMemoryRepository implements UserRepository
{
    /**
     *  Store events in memory
     **/
    private array $memory = [
        'aggregates' => [],
        'events' => [],
    ];

    /**
     *  @inheritDoc
     **/
    public function append(TracksEvents $aggregate): void
    {
        $aggregateId = $aggregate->getAggregateId();
        $events = $aggregate->getRecordedEvents();

        // Retrieve events by aggregateId. Same as select <fields> where aggregateId = <aggregateId>;
        $currentPersistedAggregate = array_filter(
            $this->memory['aggregates'],
            function ($event) use ($aggregateId) {
                return $event[0] === $aggregateId;
            }
        );

        if (!$currentPersistedAggregate) {
            $version = 0;

            $this->memory['aggregates'][] = [
                $aggregateId,
                User::class,
                $version,
            ];
        } else {
            $version = $currentPersistedAggregate[0][2];
        }

        foreach ($events as $event) {
            $this->memory['events'][] = [
                $event->getAggregateId(),
                json_encode($event->serialize()),
                ++$version
            ];
        }

        foreach ($this->memory['aggregates'] as $key => $value) {
            if ($value[0] === $aggregateId) {
                $this->memory['aggregates'][$key][2] = $version;

                break;
            }
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
                $this->memory['events'],
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
