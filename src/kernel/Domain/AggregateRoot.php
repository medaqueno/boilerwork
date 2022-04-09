#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use Kernel\Domain\ValueObjects\Identity;

abstract class AggregateRoot implements RecordsEvents, IsEventSourced
{
    use ApplyEvent;

    protected readonly Identity $aggregateId;

    protected int $version = 0;

    private array $latestRecordedEvents = [];

    final public function getAggregateId(): string
    {
        return $this->aggregateId->toPrimitive();
    }

    final public function getRecordedEvents(): array
    {
        return $this->latestRecordedEvents;
    }

    final public function clearRecordedEvents(): void
    {
        $this->latestRecordedEvents = [];
    }

    final public function currentVersion(): int
    {
        return $this->version;
    }

    final protected function increaseVersion(): void
    {
        $version = $this->currentVersion();
        $this->version = ++$version;
    }

    protected function raise(DomainEvent $event): void
    {
        $this->increaseVersion();

        $this->latestRecordedEvents[] = $event;
        $this->apply($event);

        eventsPublisher()->raise(event: $event);
    }

    /**
     * Apply DomainEvents to Aggregate to reconstitute its current state
     **/
    public static function reconstituteFrom(AggregateHistory $aggregateHistory): RecordsEvents
    {
        $aggregate = new static(
            aggregateId: $aggregateHistory->getAggregateId()
        );

        foreach ($aggregateHistory->getAggregateHistory() as $event) {
            $aggregate->increaseVersion();
            $aggregate->version = $aggregate->currentVersion();

            $aggregate->apply($event);
        }

        return $aggregate;
    }
}
