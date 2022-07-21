#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Domain\ExampleDomain;

use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\ValueObjects\Identity;
use Boilerwork\Infra\Persistence\EventStore;

interface ExampleRepository extends EventStore
{
    public function reconstituteHistoryFor(Identity $id): Example|IsEventSourced; // Return union types to accomplish interface and IDE typehinting
}
