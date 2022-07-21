#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\ExampleBoundedContext\UI\Ports\Http\ExampleDomain\ExamplePort;

return [
    // [METHOD', 'URI', 'TARGET_CLASS', [PERMISSIONS], [MIDDLEWARE]]

    ['POST', '/example', ExamplePort::class, ['Public'], []],
];
