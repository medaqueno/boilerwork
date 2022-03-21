#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

use App\Core\BC\Application\ExampleCommand;
use Kernel\UI\AbstractController;
use Swoole\Http\Request;
use Psr\Http\Message\ResponseInterface;

final class ExampleController extends AbstractController
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $this->command()->handle(
            new ExampleCommand(email: 'laotracosa@test.es', username: 'mdqn')
        );

        return responseJson(
            data: ['pelota' => 'botando']
        );
    }
}
