#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use App\Core\BC\Domain\Events\UserHasBeenApproved;
use App\Core\BC\Domain\Events\UserHasRegistered;
use App\Core\BC\Domain\ValueObjects\UserEmail;
use App\Core\BC\Domain\ValueObjects\UserName;
use Kernel\Domain\Assert;
use Kernel\Domain\AggregateRoot;
use Kernel\Domain\IsEventSourced;
use Kernel\Domain\RecordsEvents;
use Kernel\Domain\ValueObjects\Identity;

final class User extends AggregateRoot implements RecordsEvents, IsEventSourced
{
    const USER_STATUS_INITIAL = 1;
    const USER_STATUS_APPROVED = 2;

    protected int $status;

    protected function __construct(
        protected readonly Identity $userId,
    ) {
        $this->status = 0;
    }

    public static function register(
        string $userId,
        string $email,
        string $username
    ): self {

        // Check Invariants
        // Check email uniqueness in persistence
        // Check username uniqueness in persistence

        $user = new static(
            userId: new Identity($userId),
        );

        $user->recordThat(
            new UserHasRegistered(
                userId: new Identity($userId),
                email: new UserEmail($email),
                username: new UserName($username),
            )
        );

        return $user;
    }

    protected function applyUserHasRegistered(UserHasRegistered $event): void
    {
        $this->email = $event->email;
        $this->username = $event->username;
        $this->status = self::USER_STATUS_INITIAL;
    }

    public function approveUser(
        string $userId,
    ): void {

        // Check Invariants
        // Check if current status is ok to be promoted
        Assert::lazy()->tryAll()
            ->that($this->status)
            ->eq(self::USER_STATUS_INITIAL, 'User should be in initial state to be approved', 'invalid_state')
            ->verifyNow();

        $this->recordThat(
            new UserHasBeenApproved(
                userId: new Identity($userId),
            )
        );
    }

    protected function applyUserHasBeenApproved(UserHasBeenApproved $event): void
    {
        $this->status = self::USER_STATUS_APPROVED;
    }
}
