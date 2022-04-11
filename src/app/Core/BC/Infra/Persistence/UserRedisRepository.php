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

final class UserRedisRepository implements UserRepository
{
    public function __construct(public readonly RedisClient $client)
    {
    }

    public function append(RecordsEvents $aggregate): void
    {
        $aggregateId = $aggregate->getAggregateId();
        $events = $aggregate->getRecordedEvents();

        $currentPersistedAggregate = $this->client->conn->hGet('Aggregates', $aggregateId);

        $transaction = $this->client->initTransaction();

        if (!$currentPersistedAggregate) {
            $version = 0;

            $transaction->hSet(
                'Aggregates',
                $aggregateId,
                json_encode(
                    [
                        'type' => User::class,
                        'version' => $version,
                    ]
                )
            );
        } else {
            $version = (json_decode($currentPersistedAggregate, true))['version'];
        }

        if ($version + count($events) !== $aggregate->currentVersion()) {
            throw new \Kernel\Infra\Persistence\Exceptions\PersistenceException(sprintf("Expected version and aggregate version must be the same. Aggregate %s history may be corrupted.", $aggregateId));
        }

        foreach ($events as $event) {
            ++$version;

            $transaction->hSet($aggregateId, (string)$version, json_encode($event->serialize()));
        }

        $transaction->hSet(
            'Aggregates',
            $aggregateId,
            json_encode(
                [
                    'type' => User::class,
                    'version' => $version,
                ]
            )
        );

        $this->client->endTransaction();
        $this->client->putConnection($this->client->conn);

        $aggregate->clearRecordedEvents();
    }

    /**
     *  @inheritDoc
     **/
    public function getAggregateHistoryFor(Identity $aggregateId): User
    {
        $array = $this->client->conn->hGetAll($aggregateId->toPrimitive());
        // Redis has not order by, so it returns from newer to older
        $array = array_reverse($array, true);
        $this->client->putConnection($this->client->conn);

        if (count($array) === 0) {
            throw new \Exception(sprintf('No aggregate has been found with aggregateId: %s', $aggregateId->toPrimitive()));
        }

        return User::reconstituteFrom(
            new AggregateHistory(
                $aggregateId,
                // Only extract Data column specific to a Domain Event
                array_map(function ($event) {
                    return json_decode($event, true);
                }, $array)
            )
        );
    }
}
