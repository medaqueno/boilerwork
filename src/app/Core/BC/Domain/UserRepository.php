#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use Kernel\Domain\TracksEvents;
use Kernel\Domain\ValueObjects\Identity;
use Kernel\Infra\Persistence\EventStore;

interface UserRepository extends EventStore
{
    /**
     *  {@inheritDoc}
     **/
    public function append(TracksEvents $events): void;

    /**
     *  {@inheritDoc}
     **/
    public function getAggregateHistoryFor(Identity $id): User;
}
