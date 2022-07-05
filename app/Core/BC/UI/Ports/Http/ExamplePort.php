#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Http;

use App\Core\BC\Application\ExampleCommand;
use Boilerwork\System\Http\AbstractHTTPPort;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Psr\Http\Message\ResponseInterface;

final class ExamplePort extends AbstractHTTPPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        // We need synchronous execution, because of business invariants that cross Aggregate Boundaries:
        $this->command()->syncHandle(
            new ExampleCommand(
                id: $request->input('id'),
                email: $request->input('email'),
                username: $request->input('username')
            ),
        );

        return Response::empty(201);
    }
}
