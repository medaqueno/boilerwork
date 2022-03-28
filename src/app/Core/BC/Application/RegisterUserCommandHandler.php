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
        $aggregate = User::register(
            userId: $command->id,
            email: $command->email,
            username: $command->username,
        );

        $aggregate->approveUser(userId: $command->id);

        $this->userRepository->append($aggregate);

        $reconstitutedAggregate = $this->userRepository->getAggregateHistoryFor(new Identity($command->id));
        echo "\nRegisterUserCommandHandler reconstitutedAggregate\n";
        var_dump($reconstitutedAggregate);

        eventsPublisher()->releaseEvents();
    }
}
