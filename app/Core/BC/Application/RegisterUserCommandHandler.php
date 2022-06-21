#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Boilerwork\Application\CommandHandlerInterface;
use Boilerwork\Application\CommandInterface;
use Boilerwork\Domain\ValueObjects\Identity;

/**
 * @see App\Core\BC\Application\RegisterUserCommand
 **/
final class RegisterUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private UserRepository $userRepository
    ) {
    }

    public function handle(CommandInterface $command): void
    {
        $user = User::register(
            userId: (Identity::create())->toPrimitive(),
            // userId: $command->id,
            email: $command->email,
            username: $command->username,
        );

        $this->userRepository->append($user);

        eventsPublisher()->releaseEvents();
    }
}
