#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use Kernel\Application\CommandInterface;

/**
 * @see \App\Core\BC\Application\ApproveUserCommandHandler
 */
final class ApproveUserCommand implements CommandInterface
{
    public function __construct(
        public readonly string $id,
    ) {
    }
}
