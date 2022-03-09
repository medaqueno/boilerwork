#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core;

class Mailer
{
    private string $testString = 'String from Mailer';

    public function getTestString(): string
    {
        return $this->testString;
    }
}
