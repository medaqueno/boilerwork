#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\UI\Controllers\ExampleController;

return [
    ['GET', '/invoke/{var1}/{var2}', ExampleController::class],
];
