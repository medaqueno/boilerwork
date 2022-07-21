#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Domain\ExampleDomain\Events;

use Boilerwork\Domain\AbstractEvent;

final class ExampleWasCreated extends AbstractEvent
{
    protected string $topic = "example-was-created";

    public function __construct(
        public readonly string $exampleId,
        public readonly string $name,
        public readonly string $region,
    ) {
    }

    public function getAggregateId(): string
    {
        return $this->exampleId;
    }

    public function serialize(): array
    {
        return $this->wrapSerialize(
            data: [
                'exampleId' => $this->exampleId,
                'name' => $this->name,
                'region' => $this->region,
            ]
        );
    }

    public static function unserialize(array $event): self
    {
        return (new static(
            exampleId: $event['data']['exampleId'],
            name: $event['data']['name'],
            region: $event['data']['region'],
        ));
    }
}
