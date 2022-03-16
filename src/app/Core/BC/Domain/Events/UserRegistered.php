#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\Events;

use Kernel\Events\EventInterface;

final class UserRegistered implements EventInterface
{
    public function __construct(
        public readonly array $payload
    ) {
    }
}
