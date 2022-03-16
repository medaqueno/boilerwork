#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Messaging;

use App\Core\BC\Domain\Events\UserRegistered;
use Kernel\Events\EventInterface;
use Kernel\Events\EventSubscriberInterface;
use RuntimeException;

final class UserRegisteredSubscriber implements EventSubscriberInterface
{
    public function handle(EventInterface $event): void
    {
        if ($event instanceof UserRegistered === false) {
            throw new RuntimeException(sprintf('%s only accepts %s event type, %s received instead', __class__, UserRegistered::class, $event::class));
        }

        echo "\nHANDLE EVENT IN " . __CLASS__ . " : " . $event::class .  "\n";
    }

    public function isSubscribedTo(): string
    {
        return UserRegistered::class;
    }
}
