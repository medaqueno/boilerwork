#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain\ValueObjects;

use Kernel\Domain\ValueObjects\ValueObject;
use Kernel\Domain\Assert;
use Symfony\Polyfill\Uuid\Uuid as UuidImplementation;

/**
 * @internal Creates UUID using Symfony\Polyfill implementation, which turns out to be faster than pecl extension.
 **/
abstract class Uuid extends ValueObject
{
    public function __construct(
        public readonly string $value
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->notEmpty('Value must not be empty', 'uuid.notEmpty')
            ->uuid('Value must be a valid UUID', 'uuid.invalidFormat')
            ->verifyNow();
    }

    /**
     * Generate new UUID v4 value object
     **/
    public static function create(): static
    {
        return new static(UuidImplementation::uuid_create(\UUID_TYPE_RANDOM));
    }

    public function equals(ValueObject $object): bool
    {
        return $this->value === $object->value && $object instanceof self;
    }

    public function toPrimitive(): string
    {
        return $this->value;
    }
}
