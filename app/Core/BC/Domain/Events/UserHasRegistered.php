#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\Events;

use Boilerwork\Domain\AbstractEvent;
use Boilerwork\Domain\DomainEvent;

final class UserHasRegistered extends AbstractEvent implements DomainEvent
{
    protected bool $isPublic = true;

    protected ?string $queue = 'test-message/onlyQueue';

    protected ?string $exchange = null;

    public function __construct(
        public readonly string $userId,
        public readonly string $email,
        public readonly string $username,
    ) {
    }

    public function getAggregateId(): string
    {
        return $this->userId;
    }

    public function serialize(): array
    {
        return $this->wrapSerialize(
            data: [
                'userId' => $this->userId,
                'email' => $this->email,
                'username' => $this->username,
            ]
        );
    }

    public static function unserialize(array $event): self
    {
        return (new static(
            userId: $event['data']['userId'],
            email: $event['data']['email'],
            username: $event['data']['username'],
        ));
    }
}