#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use DateTimeImmutable;
use Kernel\Domain\AggregateRootInterface;
use Kernel\Domain\ValueObjects\Identity;

abstract class AbstractEvent
{
    /**
     * The Aggregate this event belongs to.
     */
}
