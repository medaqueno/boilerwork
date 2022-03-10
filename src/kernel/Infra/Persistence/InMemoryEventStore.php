#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Infra\Persistence;

class InMemoryEventStore implements EventStoreInterface
{
    /** @var array<string,iterable<Event>> */
    protected array $events = [];

    /**
     * @return StoredEvent[]|iterable<Event>
     */
    public function retrieveAll(AggregateId $id): iterable
    {
        return $this->events[(string) $id];
    }

    /**
     * Stores all recorded events of the given aggregate.
     */
    public function persist(AggregateRoot $aggregate): void
    {
        $this->events[(string) $aggregate->getId()] = new LazyCollection($aggregate->flushEvents());
    }
}
