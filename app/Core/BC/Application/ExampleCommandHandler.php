#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Boilerwork\Application\CommandHandlerInterface;
use Boilerwork\Application\CommandInterface;

/**
 * @see App\Core\BC\Application\ExampleCommand
 **/
final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function handle(CommandInterface $command): void
    {
        $user = User::register(
            userId: $command->id,
            email: $command->email,
            username: $command->username,
        );

        $this->userRepository->append($user);

        eventsPublisher()->releaseEvents();
    }
}
