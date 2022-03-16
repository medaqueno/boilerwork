#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use Kernel\Events\EventPublisher;

abstract class AggregateRoot
{
    protected int $version = 0;

    protected int $id;

    public function id(): int
    {
        return $this->id;
    }

    protected function getCurrentVersion(): int
    {
        return $this->version;
    }

    protected function increaseVersion(): void
    {
        $version = $this->getCurrentVersion();
        $this->version = ++$version;
    }

    protected function publish($event)
    {
        EventPublisher::getInstance()->publish(event: $event);
    }

    abstract public function toArray(): array;
}
