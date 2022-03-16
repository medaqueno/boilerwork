#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Events;

interface EventSubscriberInterface
{
    public function handle(EventInterface $event): void;

    public function isSubscribedTo(): string;
}
