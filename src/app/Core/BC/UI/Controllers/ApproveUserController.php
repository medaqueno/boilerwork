#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

use App\Core\BC\Application\ApproveUserCommand;
use Kernel\System\Http\Request;
use Kernel\UI\AbstractController;
use Psr\Http\Message\ResponseInterface;

final class ApproveUserController extends AbstractController
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $this->command()->handle(
            new ApproveUserCommand(
                id: $request->input('id'),
            ),
        );

        return responseEmpty();
    }
}
