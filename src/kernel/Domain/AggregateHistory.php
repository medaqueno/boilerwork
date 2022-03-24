#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use Kernel\Domain\ValueObjects\Identity;

/**
 * An AggregateRoot, that can be reconstituted from an AggregateHistory.
 */
final class AggregateHistory
{
    private $aggregateId;

    private array $history = [];

    public function __construct(Identity $aggregateId, array $events)
    {
        /** @var $event DomainEvent */
        foreach ($events as $domainEvent) {
            if (!$domainEvent->getAggregateId()->equals($aggregateId)) {
                throw new \Exception('Aggregate History Corrupted');
            }

            $this->append($domainEvent);
        }

        $this->aggregateId = $aggregateId;
    }

    public function getAggregateId(): Identity
    {
        return $this->aggregateId;
    }

    public function getAggregateHistory(): array
    {
        return $this->history;
    }

    private function append(DomainEvent $domainEvent)
    {
        $this->history[] = $domainEvent;
    }
}
