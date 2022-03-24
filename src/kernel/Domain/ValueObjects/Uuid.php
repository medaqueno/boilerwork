#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain\ValueObjects;

use Kernel\Domain\ValueObjects\ValueObject;
use Kernel\Domain\Assert;
use Symfony\Polyfill\Uuid\Uuid as UuidImplementation;

/**
 * @internal Creates UUID of different type using Symfony\Polyfill implementation, which results to be faster than pecl extension.
 **/
class Uuid extends ValueObject
{
    public function __construct(
        public readonly string $value
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->notEmpty('Value must not be empty', 'uuid_not_empty')
            ->uuid('Value must be a valid UUID', 'uuid_invalid_format')
            ->verifyNow();
    }

    /**
     * Generate new UUID v4 value object
     **/
    public static function create(): static
    {
        return new static(UuidImplementation::uuid_create(UUID_TYPE_RANDOM));
    }

    public function equals(Uuid $uuid): bool
    {
        return $this->value == $uuid->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
