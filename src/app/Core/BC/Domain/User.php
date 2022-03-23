#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use App\Core\BC\Domain\Events\UserRegistered;
use Kernel\Domain\AggregateRoot;
use Kernel\Domain\ValueObjects\Identity;

final class User extends AggregateRoot
{
    const USER_STATUS_INITIAL = 1;

    private function __construct(
        protected Identity $id,
        private string $email,
        private string $username,
        private int $status
    ) {
        $this->increaseVersion();
        $this->initializeTimestamps();
    }

    public static function register(
        string $id,
        string $email,
        string $username
    ): static {
        $entity = new static(
            id: (new Identity($id)),
            email: $email,
            username: $username,
            status: self::USER_STATUS_INITIAL,
        );

        $entity->publish(new UserRegistered($entity->toArray()));

        return $entity;
    }

    public function approveUser(): void
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id()->__toString(),
            'email' => $this->email,
            'username' => $this->username,
            'status' => $this->status,
        ];
    }
}
