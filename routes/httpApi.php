#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\UI\Ports\Http\ExamplePort;
use Boilerwork\System\Http\Middleware\NeedsAuthInfoMiddleware;

return [
    ['POST', '/registerUser', ExamplePort::class, [NeedsAuthInfoMiddleware::class]],
    // ['POST', '/registerUser', ExamplePort::class, []],
];
