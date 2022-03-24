#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain\ValueObjects;

abstract class ValueObject
{
    abstract public function __toString();

    abstract public function equals(ValueObject $object): bool;
}
