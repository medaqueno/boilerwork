#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Tasks;

interface TaskInterface
{
    public function handle();
}
