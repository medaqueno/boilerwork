#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

use App\Core\BC\Application\ExampleCommand;
use Kernel\UI\AbstractController;
use Swoole\Http\Request;

final class TestController extends AbstractController
{
    public function __invoke(Request $request, array $vars): mixed
    {
        $this->command()->handle(
            new ExampleCommand(email: 'laotracosa@test.es', username: 'mdqn')
        );

        return response_empty();
    }
}
