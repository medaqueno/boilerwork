#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System;

use Kernel\System\Server\Http;

final class RunServer
{
    public function __construct(string $server)
    {
        !defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', SWOOLE_HOOK_ALL);
        \Swoole\Runtime::enableCoroutine(true);

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
