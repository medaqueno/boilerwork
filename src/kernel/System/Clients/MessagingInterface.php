#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Clients;

interface MessagingInterface
{
    public function publish(string $message, string $queue): void;

    public function subscribe(string $queue, string $exchange = null, callable $fn): void;
}
