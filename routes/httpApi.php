#!/usr/bin/env php
<?php

declare(strict_types=1);

use App\Core\BC\UI\Ports\Http\RegisterUserPort;
use App\Core\BC\UI\Ports\Http\ApproveUserPort;

return [
    ['POST', '/registerUser', RegisterUserPort::class],
    ['POST', '/approveUser', ApproveUserPort::class],
];
