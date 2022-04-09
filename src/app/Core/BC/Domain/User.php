#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use App\Core\BC\Domain\Events\UserHasBeenApproved;
use App\Core\BC\Domain\Events\UserHasRegistered;
use App\Core\BC\Domain\Services\EmailUniqueness;
use App\Core\BC\Domain\Services\UsernameUniqueness;
use App\Core\BC\Domain\ValueObjects\UserEmail;
use App\Core\BC\Domain\ValueObjects\UserName;
use App\Core\BC\Domain\ValueObjects\UserStatus;
use Kernel\Domain\Assert;
use Kernel\Domain\AggregateRoot;
use Kernel\Domain\IsEventSourced;
use Kernel\Domain\RecordsEvents;
use Kernel\Domain\ValueObjects\Identity;

final class User extends AggregateRoot implements RecordsEvents, IsEventSourced
{
    protected UserStatus $status;

    protected function __construct(
        protected readonly Identity $aggregateId,
    ) {
    }

    public static function register(
        string $userId,
        string $email,
        string $username
    ): self {

        // Check Invariants

        // TODO: Check email uniqueness in persistence
        $emailUniqueness = app()->container()->get(EmailUniqueness::class);
        // TODO: Check username uniqueness in persistence
        $usernameUniqueness = app()->container()->get(UsernameUniqueness::class);

        Assert::lazy()->tryAll()
            ->that($emailUniqueness->isSatisfiedBy(email: $email))
            ->true('Email already exists', 'user.emailAlreadyExists')
            ->that($usernameUniqueness->isSatisfiedBy(username: $username))
            ->true('User Name already exists', 'user.usernameAlreadyExists')
            ->verifyNow();

        $user = new static(
            aggregateId: new Identity($userId),
        );

        $user->recordThat(
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

        // Check Invariants
        // Check if current status is ok to be promoted
        Assert::lazy()->tryAll()
            ->that($this->status->toPrimitive())
            ->eq(UserStatus::USER_STATUS_INITIAL, 'User should be in initial status to be approved', 'user.invalidStatusCondition')
            ->verifyNow();

        $this->recordThat(
            new UserHasBeenApproved(
                userId: (new Identity($userId))->toPrimitive(),
            )
        );
    }

    protected function applyUserHasBeenApproved(UserHasBeenApproved $event): void
    {
        $this->status = new UserStatus(UserStatus::USER_STATUS_APPROVED);
    }
}
