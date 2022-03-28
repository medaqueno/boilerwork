#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\Events;

use DateTimeImmutable;
use Kernel\Domain\DomainEvent;
use Kernel\Domain\ValueObjects\Identity;

final class UserHasBeenApproved implements DomainEvent
{
    public function __construct(
        public readonly string $userId,
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
            ],
            'version' => 0,
        ];
    }

    public static function unserialize(array $event): self
    {
        return (new static(
            userId: $event['data']['userId'],
        ));
    }
}
