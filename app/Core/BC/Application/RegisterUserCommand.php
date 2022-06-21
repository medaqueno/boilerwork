#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use Boilerwork\Application\CommandInterface;

/**
 * @see \App\Core\BC\Application\RegisterUserCommandHandler
 */
final class RegisterUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
        public readonly string $email,
        public readonly string $username,
    ) {
    }
}
