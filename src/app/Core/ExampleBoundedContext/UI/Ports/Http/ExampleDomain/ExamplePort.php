#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\UI\Ports\Http\ExampleDomain;

use App\Core\ExampleBoundedContext\Application\ExampleDomain\ExampleCommand;
use Boilerwork\System\Http\AbstractHttpPort;
use Boilerwork\System\Http\Request;
use Boilerwork\System\Http\Response;
use Psr\Http\Message\ResponseInterface;

final class ExamplePort extends AbstractHttpPort
{
    public function __invoke(Request $request, array $vars): ResponseInterface
    {
        $this->command()->handle(new ExampleCommand(
            exampleId: $request->input('id'),
            name: $request->input('name'),
            region: $request->input('region'),
        ));

        return Response::empty(202);
    }
}
