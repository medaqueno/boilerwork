#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

final class TestService implements TestServiceInterface
{
    public function debeEstar()
    {
        echo "\nDEBO ESTAR Y ESTOY\n";
    }
}
