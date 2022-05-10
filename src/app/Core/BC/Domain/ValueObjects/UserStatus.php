#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\ValueObjects;

use Kernel\Domain\ValueObjects\ValueObject;
use Kernel\Domain\Assert;

/**
 * @internal
 **/
class UserStatus extends ValueObject
{
    // Possible values
    public const USER_STATUS_INITIAL = 1;

    public const USER_STATUS_APPROVED = 2;

    public function __construct(
        public readonly int $value = self::USER_STATUS_INITIAL
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->integer('Value must be an integer', 'userStatus.invalidType')
            ->between(1, 2, 'Value must be between 1 and 2, both included', 'userStatus.invalidValue')
            ->verifyNow();
    }

    public function equals(ValueObject $object): bool
    {
        return $this->value === $object->value && $object instanceof self;
    }

    public function toPrimitive(): int
    {
        return $this->value;
    }
}
