#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Http;

use App\Core\BC\Application\ApproveUserCommand;
use Kernel\System\Http\Request;
use Kernel\UI\AbstractPort;
use Psr\Http\Message\ResponseInterface;

final class ApproveUserPort extends AbstractPort
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
