#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Boilerwork\Domain\AggregateHistory;
use Boilerwork\Domain\TracksEvents;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\System\Clients\PostgreSQLWritesClient;

final class UserPostgreSQLRepository implements UserRepository
{
    public function __construct(
        private readonly PostgreSQLWritesClient $client
    ) {
    }

    /**
     * {@inheritDoc}
     *
     * @internal
     * 1. First checks to see if an aggregate exists with the unique identifier it is to use, if there is not one it will create it and consider the current version to be zero.
     * 2. It will then attempt to do an optimistic concurrency test on the data coming in if the expected version does not match the actual version it will raise a concurrency exception.
     * 3. Providing the versions are the same, it will then loop through the events being saved and insert them into the events table, incrementing the version number by one for each event.
     * 4. Finally it will update the Aggregates table to the new current version number for the aggregate. It is important to note that these operations are in a transaction as it is required to insure that optimistic concurrency amongst other things works in a distributed environment.
     *
     * Pseudo code:
     *
        Begin
            version = SELECT version from aggregates where AggregateId = ‘’
            if version is null
                Insert into aggregates
                version = 0
            end
            if expectedversion != version
                raise concurrency problem
            foreach event
                insert event with incremented version number
            update aggregate with last version number
        End Transaction
     *
     * extracted from CQRS Documents by Greg Young - https://cqrs.wordpress.com/documents/building-event-storage/
     **/
    public function append(TracksEvents $aggregate): void
    {
        // go(function () use ($aggregate) {

        $aggregateId = $aggregate->getAggregateId();
        $events = $aggregate->getRecordedEvents();

        $this->client->getConnection();
        $this->client->initTransaction();

        $result = $this->client->run('SELECT "version" FROM "aggregates" WHERE "aggregate_id" = $1', [$aggregateId]);
        $currentPersistedAggregate =  $this->client->fetchAll($result);

        if (!$currentPersistedAggregate) {
            $version = 0;

            $this->client->run(
                'INSERT INTO "aggregates" ("aggregate_id", "type", "version") VALUES($1, $2, $3)',
                [
                    $aggregateId,
                    User::class,
                    $version // Shall be updated after persisting events
                ]
            );
        } else {
            $version = $currentPersistedAggregate[0]['version'];
        }

        if ($version + count($events) !== $aggregate->currentVersion()) {
            throw new \Boilerwork\Infra\Persistence\Exceptions\PersistenceException(sprintf("Expected version and aggregate version must be the same. Aggregate %s history may be corrupted.", $aggregateId), 409);
        }

        foreach ($events as $event) {
            $this->client->run(
                'INSERT INTO "events" ("aggregate_id", "data", "version") VALUES($1, $2, $3)',
                [
                    $event->getAggregateId(),
                    json_encode($event->serialize()),
                    ++$version
                ]
            );
        }

        $this->client->run(
            'UPDATE "aggregates" SET "version" = $1 WHERE "aggregate_id" = $2',
            [
                $version,
                $aggregateId
            ],
        );

        $this->client->endTransaction();

        $this->client->putConnection();

        $aggregate->clearRecordedEvents();
        // });
    }

    /**
     *  @inheritDoc
     **/
    public function getAggregateHistoryFor(Identity $aggregateId): User
    {
        $this->client->getConnection();

        $query = $this->client->run('SELECT "data" FROM "events" WHERE "aggregate_id" = $1 ORDER BY "version"', [$aggregateId->toPrimitive()]);

        $array  = $this->client->fetchAll($query);

        $this->client->putConnection();

        if (count($array) === 0) {
            throw new \Exception(sprintf('No aggregate has been found with aggregateId: %s', $aggregateId->toPrimitive()), 404);
        }

        return User::reconstituteFrom(
            new AggregateHistory(
                $aggregateId,
                // Only extract Data column specific to a Domain Event
                array_map(function (array $event) {
                    return json_decode($event['data'], true);
                }, $array)
            )
        );
    }
}
