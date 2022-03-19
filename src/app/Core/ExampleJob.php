#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core;

use Kernel\System\Jobs\JobInterface;
use App\Core\Mailer;

final class ExampleJob implements JobInterface
{
    public function __construct(public Mailer $mailer)
    {
    }

    public function handle(): void
    {
        $date = date('Y-m-d H:i:s', time());
        echo "\nESTAMOS EN EL JOB " . $date . " \n";
    }
}
