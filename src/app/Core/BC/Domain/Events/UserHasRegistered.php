#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\Events;

use App\Core\BC\Domain\ValueObjects\UserEmail;
use App\Core\BC\Domain\ValueObjects\UserName;
use Kernel\Domain\DomainEvent;
use Kernel\Domain\ValueObjects\Identity;

final class UserHasRegistered implements DomainEvent
{
    public function __construct(
        public readonly Identity $userId,
        public readonly UserEmail $email,
        public readonly UserName $username,
    ) {
    }

    public function getAggregateId(): Identity
    {
        return $this->userId;
    }
}
