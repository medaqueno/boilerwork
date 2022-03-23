#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use Kernel\Domain\ValueObjects\Identity;
use Kernel\Events\EventPublisher;
use DateTimeImmutable;
use DateTimeInterface;

abstract class AggregateRoot
{
    protected int $version = 0;

    protected Identity $id;

    protected ?DateTimeInterface $createdAt = null;
    protected ?DateTimeInterface $updatedAt = null;

    final protected function getCurrentVersion(): int
    {
        return $this->version;
    }

    final protected function increaseVersion(): void
    {
        $version = $this->getCurrentVersion();
        $this->version = ++$version;
    }

    final protected function publish($event)
    {
        EventPublisher::getInstance()->publish(event: $event);
    }

    final public function id(): Identity
    {
        return $this->id;
    }

    final public function equals(object $object): bool
    {
        if (!$object instanceof AggregateRoot) {
            return false;
        }

        return $this->id->equals($object->id());
    }

    final protected function initializeTimestamps(): void
    {
        if ($this->createdAt && $this->updatedAt) {
            return;
        }

        $this->createdAt = $this->updatedAt = new DateTimeImmutable('now');
    }

    final protected function changeLastUpdatedToNow(): void
    {
        $this->updatedAt = new DateTimeImmutable('now');
    }


    abstract public function toArray(): array;
}
