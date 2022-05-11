#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\Events;

use Kernel\Domain\AbstractEvent;
use Kernel\Domain\DomainEvent;

final class UserHasBeenApproved extends AbstractEvent implements DomainEvent
{
    protected bool $isPublic = false;

    protected ?string $queue = 'user-has-been-approved';

    protected ?string $exchange;

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
        return $this->wrapSerialize(
            data: [
                'userId' => $this->userId,
            ]
        );
    }

    public static function unserialize(array $event): self
    {
        return (new static(
            userId: $event['data']['userId'],
        ));
    }
}
