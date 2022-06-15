#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\UI\Ports\WebSocketPort;

return [
    ['/customMethod/{var1}', [WebSocketPort::class, 'customMethod']],
    ['/invoke/{var1}/{var2}', WebSocketPort::class],
];
