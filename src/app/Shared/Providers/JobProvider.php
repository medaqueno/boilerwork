#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use Kernel\System\Jobs\AbstractJobScheduler;

final class JobProvider extends AbstractJobScheduler
{
    /**
     * @inheritDoc
     */
    protected array $jobs = [
        [\App\Core\BC\Infra\Jobs\ExampleJob::class, [self::INTERVAL_EVERY_MINUTE, null]],
    ];
}
