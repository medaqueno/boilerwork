#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Infra\Persistence;

use Kernel\Domain\AggregateHistory;
use Kernel\Domain\ValueObjects\Identity;

interface EventStore
{
    /**
     *  Add Events to Persistence
     **/
    public function append(array $events): void;

    /**
     *  Get Event Stream in persistence where id = X
     **/
    public function getAggregateHistoryFor(Identity $id): AggregateHistory;
}
