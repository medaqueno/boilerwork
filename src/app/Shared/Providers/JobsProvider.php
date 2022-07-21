#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use Boilerwork\System\Jobs\AbstractJobScheduler;

final class JobsProvider extends AbstractJobScheduler
{
    /**
     * @inheritDoc
     */
    protected array $jobs = [
        // [\App\Core\ExampleBoundedContext\ExampleDomain\Infra\Jobs\ExampleJob::class, [self::INTERVAL_EVERY_MINUTE, null]],
    ];
}
