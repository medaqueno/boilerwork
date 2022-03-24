#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Events;

use Kernel\Domain\DomainEvent;

interface EventSubscriberInterface
{
    public function handle(DomainEvent $event): void;

    public function isSubscribedTo(): string;
}
