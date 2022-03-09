#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\UI;

use Kernel\Application\CommandBus;
use Swoole\Http\Request;

abstract class AbstractController
{
    abstract public function __invoke(Request $request, array $vars): mixed;

    public function command(): CommandBus
    {
        return new CommandBus();
    }
}
