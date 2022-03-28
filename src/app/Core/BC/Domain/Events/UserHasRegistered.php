#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\Events;

use DateTimeImmutable;
use Kernel\Domain\DomainEvent;

final class UserHasRegistered implements DomainEvent
{
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
        return [
            'aggregateId' => $this->getAggregateId(),
            'event' => [
                'type' => static::class,
                'ocurredOn' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM),
            ],
            'data' => [
                'userId' => $this->userId,
                'email' => $this->email,
                'username' => $this->username,
            ],
            'version' => 0,
        ];
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
