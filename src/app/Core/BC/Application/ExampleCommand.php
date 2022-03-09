#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use Kernel\Application\CommandInterface;

/**
 * @see ExampleCommandHandler
 */
final class ExampleCommand implements CommandInterface
{
    public function __construct(
        public readonly string $email,
        public readonly string $username,
    ) {
    }
}
