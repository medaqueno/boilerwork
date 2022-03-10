#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System;

use Kernel\System\Server\Http;

final class RunServer
{
    public function __construct(string $server)
    {
        switch ($server) {
            case 'http':
                $className = Http::class;
                break;
            default:
                $className = Http::class;
        }

        new $className();
    }
}
