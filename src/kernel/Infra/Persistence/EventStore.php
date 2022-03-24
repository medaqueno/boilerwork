#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Infra\Persistence;

use Kernel\Domain\AggregateHistory;
use Kernel\Domain\ValueObjects\Identity;

interface EventStore
{
    public function commit(array $events): void;

    public function getAggregateHistoryFor(Identity $id): AggregateHistory;
}
