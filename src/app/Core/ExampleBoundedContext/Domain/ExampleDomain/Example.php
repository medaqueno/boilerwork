#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Domain\ExampleDomain;

use App\Core\ExampleBoundedContext\Domain\ExampleDomain\Events\ExampleWasCreated;
use App\Core\ExampleBoundedContext\Domain\ExampleDomain\ValueObjects\Name;
use App\Core\ExampleBoundedContext\Domain\ExampleDomain\ValueObjects\Region;
use Boilerwork\Domain\AggregateRoot;
use Boilerwork\Domain\Assert;
use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\IsEventSourcedTrait;
use Boilerwork\Domain\TracksEvents;
use Boilerwork\Domain\TracksEventsTrait;
use Boilerwork\Domain\ValueObjects\Identity;


final class Example extends AggregateRoot implements TracksEvents, IsEventSourced
{
    use TracksEventsTrait, IsEventSourcedTrait;

    private Name $name;
    private Region $region;

    public static function create(
        string $exampleId,
        string $name,
        string $region,
    ): self {

        // Check Business Invariants

        // Build Aggregate
        $example = new static(
            aggregateId: new Identity($exampleId),
        );

        $example->raise(
            new ExampleWasCreated(
                exampleId: (new Identity($exampleId))->toPrimitive(),
                name: (new Name($name))->toPrimitive(),
                region: (new Region($region))->toPrimitive(),
            )
        );

        return $example;
    }

    protected function applyExampleWasCreated(ExampleWasCreated $event): void
    {
        $this->exampleId = new Identity($event->exampleId);
        $this->name = new Name($event->name);
        $this->region = new Region($event->region);
    }

    private function __construct(
        protected readonly Identity $aggregateId,
    ) {
    }
}
