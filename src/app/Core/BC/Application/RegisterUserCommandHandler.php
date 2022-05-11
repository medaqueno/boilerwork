#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\Application\CommandHandlerInterface;
use Kernel\Application\CommandInterface;
use Kernel\Domain\ValueObjects\Identity;

/**
 * @see App\Core\BC\Application\RegisterUserCommand
 **/
final class RegisterUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(CommandInterface $command): void
    {
        // Check Cross Boundaries business invariants

        // TODO: Check email uniqueness in persistence
        // $emailUniqueness = app()->container()->get(EmailUniqueness::class);
        // // TODO: Check username uniqueness in persistence
        // $usernameUniqueness = app()->container()->get(UsernameUniqueness::class);
        // Assert::lazy()->tryAll()
        //     ->that($emailUniqueness->isSatisfiedBy(email: $email))
        //     ->true('Email already exists', 'user.emailAlreadyExists')
        //     ->that($usernameUniqueness->isSatisfiedBy(username: $username))
        //     ->true('User Name already exists', 'user.usernameAlreadyExists')
        //     ->verifyNow();

        $user = User::register(
            userId: (Identity::create())->toPrimitive(),
            // userId: $command->id,
            email: $command->email,
            username: $command->username,
        );

        $this->userRepository->append($user);

        eventsPublisher()->releaseEvents();
    }

    private function checkCrossBoundariesInvariants(): void
    {
    }
}
