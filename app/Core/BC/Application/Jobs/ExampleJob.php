#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application\Jobs;

use Boilerwork\System\Jobs\JobInterface;

final class ExampleJob implements JobInterface
{
    public function handle(): void
    {
        $date = date('Y-m-d H:i:s', time());
        echo "\n" . __CLASS__ . " :: "   . $date . " \n";
    }
}
