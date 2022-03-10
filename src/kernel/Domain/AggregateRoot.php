#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

abstract class AggregateRoot
{
    private int $id;

    private int $version;

    protected function id(): int
    {
        return $this->id;
    }

    protected function getVersion(): int
    {
        return $this->version;
    }

    protected function increaseVersion(): void
    {
        $this->version = ++$this->getVersion();
    }
}
