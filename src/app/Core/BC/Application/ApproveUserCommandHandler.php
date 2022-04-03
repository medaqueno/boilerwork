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
        $reconstitutedAggregate = $this->userRepository->getAggregateHistoryFor(new Identity($command->id));
        // echo "\nApproveUserCommandHandler reconstitutedAggregate\n";
        var_dump($reconstitutedAggregate->getRecordedEvents());

        // $reconstitutedAggregate->approveUser(userId: $command->id);

        // $this->userRepository->append($reconstitutedAggregate);

        eventsPublisher()->releaseEvents();


        // var_dump($reconstitutedAggregate);
    }
}
