#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use App\Core\BC\Domain\UserRepository;
use Kernel\Application\CommandHandlerInterface;
use Kernel\Application\CommandInterface;
use Kernel\Domain\ValueObjects\Identity;

/**
 * @see App\Core\BC\Application\ApproveUserCommand
 **/
final class ApproveUserCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(CommandInterface $command): void
    {
        // Only testing. Another command
        // Reconstitute Aggregate
        $user = $this->userRepository->getAggregateHistoryFor(new Identity($command->id));

        var_dump($user);

        $user->approveUser(userId: $command->id);

        $this->userRepository->append($user);

        eventsPublisher()->releaseEvents();
    }
}
