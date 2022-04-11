#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

use App\Core\BC\Application\RegisterUserCommand;
use Kernel\Domain\ValueObjects\Identity;
use Kernel\System\Http\Request;
use Kernel\UI\AbstractController;
use Psr\Http\Message\ResponseInterface;

final class RegisterUserController extends AbstractController
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $this->command()->handle(
            new RegisterUserCommand(
                id: (Identity::create())->toPrimitive(),
                email: $request->input('email'),
                username: $request->input('username')
            ),
        );

        return responseEmpty();
    }
}
