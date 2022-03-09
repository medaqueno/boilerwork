#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core;

use Kernel\Tasks\TaskInterface;
use  App\Core\Mailer;

final class ExampleTask implements TaskInterface
{

    // public function handle()
    // {
    //     $date = date('Y-m-d H:i:s', time());
    //     echo "\nESTAMOS EN EL TASK " . $date . " \n";
    //     logger("ESTAMOS EN EL TASK");
    // }

    public function __construct(public Mailer $mailer)
    {
    }

    public function handle(): void
    {
        $date = date('Y-m-d H:i:s', time());
        echo "\nESTAMOS EN EL TASK " . $date . " \n";
        logger("ESTAMOS EN EL TASK " . $this->mailer->getTestString());
    }
}
