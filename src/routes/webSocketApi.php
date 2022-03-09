#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\UI\Controllers\WebSocketController;

return [
    ['/customMethod/{var1}', [WebSocketController::class, 'customMethod']],
    ['/invoke/{var1}/{var2}', WebSocketController::class],
];
