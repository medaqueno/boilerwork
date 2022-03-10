#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Infra\Persistence;


interface EventStoreInterface
{
    /**
     * Returns a stream of stored events for the given aggregate root.
     * Each implementation can run their own optimizations for this
     * (e.g. only load events starting with the current version).
     *
     * @return StoredEvent[]|iterable<StoredEvent>
     */
    public function retrieveAll(AggregateId $aggregate): iterable;

    /**
     * Stores all recorded events of the given aggregate.
     * The implementation may choose to do single or batched queries.
     *
     * In case another aggregate instance already saved its events
     * an concurrent modification exception will be thrown.
     *
     * @throws ConcurrentModificationException
     */
    public function persist(AggregateRoot $aggregate): void;
}
