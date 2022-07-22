#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Domain\ExampleDomain\ValueObjects;

use Boilerwork\Domain\ValueObjects\ValueObject;
use Boilerwork\Domain\Assert;

/**
 * @internal
 **/
final class Region extends ValueObject
{
    public function __construct(
        private string $value
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->string('Value must be a string', 'exampleRegion.invalidType')
            ->maxLength(2, 'Value must be 2 characters length', 'exampleRegion.invalidLength')
            ->verifyNow();

        // Process values
        $this->value = mb_strtoupper($value);
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
