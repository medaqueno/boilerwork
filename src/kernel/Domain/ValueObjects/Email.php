#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain\ValueObjects;

use Kernel\Domain\ValueObjects\ValueObject;
use Kernel\Domain\Assert;

/**
 * @internal
 **/
class Email extends ValueObject
{
    public function __construct(
        public readonly string $value
    ) {
        Assert::lazy()->tryAll()
            ->that($value)
            ->email('Value must be a valid email', 'email_invalid')
            ->verifyNow();
    }

    public function equals(Email $value): bool
    {
        return $this->value == $value->value;
    }

    public function __toString(): string
    {
        return $this->value;
    }

    public function account(): string
    {
        return mb_substr($this->value, 0, mb_strpos($this->value, '@'));
    }

    public function domain(): string
    {
        return mb_substr($this->value, mb_strpos($this->value, '@') + 1);
    }
}
