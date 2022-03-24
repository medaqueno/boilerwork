#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

abstract class AggregateRoot implements RecordsEvents, IsEventSourced
{
    use ApplyEvent;

    protected int $version = 0;

    private array $latestRecordedEvents = [];

    protected function recordThat(DomainEvent $event): void
    {
        $this->latestRecordedEvents[] = $event;
        $this->apply($event);

        eventsPublisher()->recordThat(event: $event);
    }

    public function getRecordedEvents(): array
    {
        return $this->latestRecordedEvents;
    }

    public function clearRecordedEvents(): void
    {
        $this->latestRecordedEvents = [];
    }

    public static function reconstituteFrom(AggregateHistory $aggregateHistory): RecordsEvents
    {
        // var_dump($aggregateHistory);
        $aggregateId = $aggregateHistory->getAggregateId();
        $aggregate = new static($aggregateId);

        foreach ($aggregateHistory->getAggregateHistory() as $event) {
            $aggregate->apply($event);
        }

        return $aggregate;
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
}
