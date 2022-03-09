#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use Kernel\Tasks\AbstractTaskScheduler;

final class TaskProvider extends AbstractTaskScheduler
{
    /**
     * @inheritDoc
     */
    protected array $tasks = [
        [\App\Core\ExampleTask::class, [self::INTERVAL_EVERY_MINUTE, null]],
    ];
}
