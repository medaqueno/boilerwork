#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Infra\Projections;

use App\Core\ExampleBoundedContext\Domain\ExampleDomain\Events\ExampleWasCreated;
use Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLReadsClient;
use Boilerwork\System\Messaging\AbstractMessageSubscriberPort;
use Boilerwork\System\Messaging\Message;
//
final class ExampleProjection extends AbstractMessageSubscriberPort
{
    private string $projectionName = 'all_examples';

    public function __construct(
        private PostgreSQLReadsClient $client
    ) {
    }

    public function __invoke(Message $message): void
    {
        $message = $message->getParsedPayload();

        match ($message->type) {
            ExampleWasCreated::class => $this->exampleWasCreated($message),
            default => '',
        };
    }

    private function exampleWasCreated($message)
    {
        $this->client->getConnection();
        $this->client->run(
            'INSERT INTO ' . $this->projectionName . ' (
            "aggregate_id",
            "name",
            "region",
            "created_at",
            "updated_at"
            ) VALUES($1, $2, $3, $4, $5)',
            [
                $message->aggregateId,
                $message->data->name,
                $message->data->region,
                $message->ocurredOn,
                $message->ocurredOn
            ]
        );
        $this->client->putConnection();
    }
}
