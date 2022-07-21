#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Application\ExampleDomain;

use Boilerwork\Application\CommandInterface;

/**
 * @see \App\Core\ExampleBoundedContext\Application\ExampleDomain\ExampleCommandHandler
 */
final class ExampleCommand implements CommandInterface
{
    public function __construct(
        public readonly string $exampleId,
        public readonly string $name,
        public readonly string $region,
    ) {
    }
}
