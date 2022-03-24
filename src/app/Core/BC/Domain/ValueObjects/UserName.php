#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\ValueObjects;

use Assert\Assert;
use Kernel\Domain\ValueObjects\ValueObject;

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
            ->string('Value must a string', 'invalid_type')
            ->betweenLength(4, 20, 'Value must be a valid email', 'invalid_length')
            ->verifyNow();
    }

    public function equals(UserName $value): bool
    {
        return $this->value == $value->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }
}
