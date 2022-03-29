#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use DateTimeImmutable;

abstract class AbstractEvent implements DomainEvent
{
    public function wrapSerialize(array $data): array
    {
        return [
            'aggregateId' => $this->getAggregateId(),
            // 'aggregateVersion' => $this->getAggregateVersion(),
            'type' => static::class,
            'ocurredOn' => (new DateTimeImmutable())->format(DateTimeImmutable::ATOM),
            'data' => $data,
        ];
    }
}
