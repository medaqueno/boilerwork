#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Infra\Persistence\EventStore;

interface UserRepository extends EventStore
{
    public function reconstituteHistoryFor(Identity $id): User|IsEventSourced; // Return union types to accomplish interface and IDE typehinting
}
