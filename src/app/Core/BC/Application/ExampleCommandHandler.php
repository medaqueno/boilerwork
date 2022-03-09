#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use Kernel\Application\CommandHandlerInterface;
use Kernel\Application\CommandInterface;

final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function __construct()
    {
    }

    public function handle(CommandInterface $command): void
    {

        // var_dump($command);
    }
}
