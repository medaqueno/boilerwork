#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use Assert\Assert as BaseAssertion;

class Assertion extends BaseAssertion
{
    protected static $lazyAssertionExceptionClass = '\Kernel\Domain\CustomAssertionFailedException';
}
