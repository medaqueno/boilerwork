#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

use Swoole\Http\Request;

final class WebSocketController
{
    public function __invoke(Request $request, array $vars): array
    {
        return [
            'message' => 'WS Invoke method',
            'data' => [
                'request' => $request->get['parameter1'] ?? null,
                'vars' => $vars
            ]

        ];
    }

    public function customMethod(Request $request, array $vars): array
    {

        return [
            'message' => 'WS customMethod',
            'data' => [
                'request' => $request->get ?? null,
                'vars' => $vars
            ]
        ];
    }
}
