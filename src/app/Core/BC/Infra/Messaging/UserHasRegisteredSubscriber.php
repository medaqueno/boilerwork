#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Messaging;

use App\Core\BC\Domain\Events\UserHasRegistered;
use Kernel\Domain\DomainEvent;
use Kernel\Events\EventSubscriberInterface;
use RuntimeException;

final class UserHasRegisteredSubscriber implements EventSubscriberInterface
{
    public function handle(DomainEvent $event): void
    {
        if ($event instanceof UserHasRegistered === false) {
            throw new RuntimeException(sprintf('%s only accepts %s event type, %s received instead', __class__, $this->isSubscribedTo(), $event::class));
        }

        // Simulate heavy load
        // sleep(3);
        echo "\nHANDLE EVENT WITH SLEEP IN " . __CLASS__ . " : " . $event::class .  ". A COMMAND SHOULD BE TRIGGERED TO DO ANYTHING\n";
    }

    public function isSubscribedTo(): string
    {
        return UserHasRegistered::class;
    }
}
