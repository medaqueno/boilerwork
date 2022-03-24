#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use Kernel\Application\CommandHandlerInterface;
use Kernel\Application\CommandInterface;

/**
 * @see App\Core\BC\Application\ApproveUserCommand
 **/
final class ApproveUserCommandHandler implements CommandHandlerInterface
{
    // public function __construct(private UserRepository $userRepository)
    // {
    // }

    public function handle(CommandInterface $command): void
    {
    }
}
