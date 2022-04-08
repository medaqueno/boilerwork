#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\Domain\AggregateHistory;
use Kernel\Domain\RecordsEvents;
use Kernel\Domain\ValueObjects\Identity;
use Kernel\System\Clients\PostgreSQLClient;

final class UserPostgreSQLRepository implements UserRepository
{
    public function __construct(private PostgreSQLClient $client)
    {
    }

    /**
     *  @inheritDoc
     **/
    public function append(RecordsEvents $aggregate): void
    {
        $events = $aggregate->getRecordedEvents();

        $this->client->initTransaction();

        foreach ($events as $event) {
            $this->client->run(
                'INSERT INTO "events" ("aggregateId", "data", "version") VALUES($1, $2, $3)',
                [
                    $event->getAggregateId(),
                    json_encode($event->serialize()),
                    $aggregate->currentVersion()
                ]
            );
        }

        $this->client->endTransaction();

        $aggregate->clearRecordedEvents();
    }

    /**
     *  @inheritDoc
     **/
    public function getAggregateHistoryFor(Identity $aggregateId): User
    {
        $query = $this->client->run('SELECT "data" FROM "events" WHERE "aggregateId" = $1 ORDER BY "version"', [$aggregateId->toPrimitive()]);

        $array  = $this->client->fetchAll($query);

        if (count($array) === 0) {
            throw new \Exception(sprintf('No aggregate has been found with aggregateId: %s', $aggregateId->toPrimitive()));
        }

        return User::reconstituteFrom(
            new AggregateHistory(
                $aggregateId,
                // Only extract Data column specific to a Domain Event
                array_map(function (array $event) {
                    return json_decode($event[0], true);
                }, $array)
            )
        );
    }
}
