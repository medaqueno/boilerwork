#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

use App\Core\BC\Application\ExampleCommand;
use Kernel\Application\CommandBus;
use Kernel\UI\AbstractController;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

final class ExampleController extends AbstractController
{
    public function __invoke(ServerRequestInterface $request, array $vars): ResponseInterface
    {
        $this->command()->handle(
            new ExampleCommand(email: 'laotracosa@test.es', username: 'mdqn')
        );

        return responseEmpty()->withStatus(200);
    }
}
