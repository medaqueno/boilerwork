#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Helpers;

trait Singleton
{
    private static $instance;

    public static function getInstance(...$args)
    {
        if (!isset(self::$instance)) {
            self::$instance = new static(...$args);
        }
        return self::$instance;
    }
}
