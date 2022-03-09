#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Controllers;

final class TestHandler
{
    public function __construct(public TestServiceInterface $service)
    {
        echo "\nY Hemos llegado hasta aquí\n";
    }

    public function __invoke(): string
    {
        echo "\nESTAMOS EN EL __invoke\n";
        $this->service->debeEstar();
        return "desde el invoke";
    }

    public function metodo(): string
    {
        // $this->service->debeEstar();
        echo "\nMUY BIEN HOMBRE\n";
        return "\nALGODELMETODO";
    }
}
