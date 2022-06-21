#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use App\Core\BC\Domain\Events\UserHasBeenApproved;
use App\Core\BC\Domain\Events\UserHasRegistered;
use App\Core\BC\Domain\ValueObjects\UserEmail;
use App\Core\BC\Domain\ValueObjects\UserName;
use App\Core\BC\Domain\ValueObjects\UserStatus;
use Boilerwork\Domain\Assert;
use Boilerwork\Domain\AggregateRoot;
use Boilerwork\Domain\IsEventSourced;
use Boilerwork\Domain\IsEventSourcedTrait;
use Boilerwork\Domain\TracksEvents;
use Boilerwork\Domain\TracksEventsTrait;
use Boilerwork\Domain\ValueObjects\Identity;

final class User extends AggregateRoot implements TracksEvents, IsEventSourced
{
    use TracksEventsTrait, IsEventSourcedTrait;

    private UserStatus $status;
    private UserEmail $email;
    private UserName $username;

    public static function register(
        string $userId,
        string $email,
        string $username
    ): self {
        $user = new static(
            aggregateId: new Identity($userId),
        );

        $user->raise(
            new UserHasRegistered(
                userId: (new Identity($userId))->toPrimitive(),
                email: (new UserEmail($email))->toPrimitive(),
                username: (new UserName($username))->toPrimitive(),
            )
        );

        return $user;
    }

    protected function applyUserHasRegistered(UserHasRegistered $event): void
    {
        $this->email = new UserEmail($event->email);
        $this->username = new UserName($event->username);
        $this->status = new UserStatus(UserStatus::USER_STATUS_INITIAL);
    }

    public function approveUser(
        string $userId,
    ): void {

        // Check Aggregate Boundary Invariants:
        // Check if current status is OK to be promoted
        Assert::lazy()->tryAll()
            ->that($this->status->toPrimitive())
            ->eq(
                UserStatus::USER_STATUS_INITIAL,
                'User should be in initial status to be approved',
                'user.invalidStatusCondition'
            )
            ->verifyNow();

        $this->raise(
            new UserHasBeenApproved(
                userId: (new Identity($userId))->toPrimitive(),
            )
        );
    }

    protected function applyUserHasBeenApproved(UserHasBeenApproved $event): void
    {
        $this->status = new UserStatus(UserStatus::USER_STATUS_APPROVED);
    }

    private function __construct(
        protected readonly Identity $aggregateId,
    ) {
    }
}
