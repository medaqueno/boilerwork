#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\UI\Controllers\TestController;

return [
    ['GET', '/customMethod/{var1}', [TestController::class, 'customMethod']],
    ['GET', '/invoke/{var1}/{var2}', TestController::class],
];
