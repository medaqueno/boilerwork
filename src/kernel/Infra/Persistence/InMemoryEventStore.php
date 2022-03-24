#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Infra\Persistence;

use Kernel\Domain\AggregateHistory;
use Kernel\Domain\DomainEvent;
use Kernel\Domain\ValueObjects\Identity;

final class InMemoryEventStore implements EventStore
{
    /**
     *  Store events in memory
     **/
    private array $events = [];

    /**
     *  @inheritDoc
     **/
    public function append($events): void
    {
        foreach ($events as $event) {
            $this->events[] = $event;
        }
    }

    /**
     *  @inheritDoc
     **/
    public function getAggregateHistoryFor(Identity $id): AggregateHistory
    {
        return new AggregateHistory(
            $id,
            array_filter(
                $this->events,
                function (DomainEvent $event) use ($id) {
                    return $event->getAggregateId()->equals($id);
                }
            )
        );
    }
}
