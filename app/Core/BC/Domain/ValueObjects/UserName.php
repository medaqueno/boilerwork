#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\ValueObjects;

use Boilerwork\Domain\ValueObjects\ValueObject;
use Boilerwork\Domain\Assert;

/**
 * @internal
 **/
class UserName extends ValueObject
{
    public function __construct(
        public readonly string $value
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->string('Value must be a string', 'userName.invalidType')
            ->betweenLength(4, 20, 'Value must be between 4 and 20 characters, both included', 'userName.invalidLength')
            ->verifyNow();
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
