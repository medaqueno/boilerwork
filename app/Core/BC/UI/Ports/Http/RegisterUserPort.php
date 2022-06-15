#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Http;

use App\Core\BC\Application\RegisterUserCommand;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Boilerwork\UI\AbstractHTTPPort;
use Psr\Http\Message\ResponseInterface;

final class RegisterUserPort extends AbstractHTTPPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        // We need synchronous execution, because of business invariants that cross Aggregate Boundaries:
        // Email/Username Uniqueness across aggregates
        $this->command()->syncHandle(
            new RegisterUserCommand(
                id: $request->input('id'),
                email: $request->input('email'),
                username: $request->input('username')
            ),
        );

        return Response::empty(201);
    }
}