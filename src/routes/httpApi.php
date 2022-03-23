#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\UI\Controllers\RegisterUserController;

return [
    ['POST', '/registerUser', RegisterUserController::class],
    // ['GET', '/invoke', RegisterUserController::class],
];
