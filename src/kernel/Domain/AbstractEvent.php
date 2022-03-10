#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use DateTimeImmutable;

abstract class AbstractEvent
{
    private string $type;

    private float $time;

    public function __construct(
        private array $payload = [],
        private ?AggregateRoot $aggregate = null,
    ) {
        $this->time = new DateTimeImmutable();
        $this->type = static::class;
    }

    public function toArray(): array
    {
        return [
            'aggregate' => [
                // 'class' => $this->aggregate ? $this->aggregate->class() : null,
                'id'    => $this->aggregate ? $this->aggregate->id() : null,
            ],
            'event'     => [
                'class' => $this->type,
                'name'  => $this->getName($this->type),
                'time'  => $this->time,
            ],
            'payload'   => $this->payload,
        ];
    }

    private function getName(mixed $event): string
    {
        if ($pos = strrpos($event, '\\')) {
            $eventName = substr($event, $pos + 1);
        } else {
            $eventName = $event;
        }

        return $eventName;
    }
}
