#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Projections;

use App\Core\BC\Domain\Events\UserHasRegistered;
use Boilerwork\Domain\DomainEvent;
use Boilerwork\Events\EventSubscriberInterface;
use RuntimeException;

final class UserEmailUniqueness implements EventSubscriberInterface
{
    public function handle(DomainEvent $event): void
    {
        if ($event instanceof UserHasRegistered === false) {
            throw new RuntimeException(sprintf('%s only accepts %s event type, %s received instead', __class__, UserHasRegistered::class, $event::class));
        }

        // Simulate heavy load
        // sleep(3);
        echo "\nHANDLE INTERNAL EVENT WITH SLEEP IN " . __CLASS__ . " : " . $event::class .  ". A COMMAND SHOULD BE TRIGGERED TO DO ANYTHING\n";
    }

    public function isSubscribedTo(): string
    {
        return UserHasRegistered::class;
    }
}