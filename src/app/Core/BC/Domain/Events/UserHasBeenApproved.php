#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\Events;

use Kernel\Domain\DomainEvent;
use Kernel\Domain\ValueObjects\Identity;

final class UserHasBeenApproved implements DomainEvent
{
    public function __construct(
        public readonly Identity $userId,
    ) {
    }

    public function getAggregateId(): Identity
    {
        return $this->userId;
    }
}
