#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Tasks;

use App\Shared\Providers\TaskProvider;
use Swoole\Timer;

final class TaskScheduler
{
    private const LOOP_INTERVAL = 10; // Set Maximum each 30 seconds or task set to be repeated each minute may not be executed

    public function __construct(
        private TaskProvider $taskProvider,
    ) {
        Timer::tick(self::LOOP_INTERVAL * 1000, function () {
            $this->taskProvider->run();
        });
    }
}
