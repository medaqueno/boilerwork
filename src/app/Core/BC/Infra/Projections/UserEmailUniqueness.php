#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Projections;

use App\Core\BC\Domain\Events\UserHasRegistered;
use Kernel\Domain\DomainEvent;
use Kernel\Events\EventSubscriberInterface;
use RuntimeException;

final class UserEmailUniqueness implements EventSubscriberInterface
{
    public function __construct()
    {
    }

    public function handle(DomainEvent $event): void
    {
        if ($event instanceof UserHasRegistered === false) {
            throw new RuntimeException(sprintf('%s only accepts %s event type, %s received instead', __class__, UserHasRegistered::class, $event::class));
        }

        // Simulate heavy load
        // sleep(3);
        echo "\nHANDLE EVENT WITH SLEEP IN " . __CLASS__ . " : " . $event::class .  ". A COMMAND SHOULD BE TRIGGERED TO DO ANYTHING\n";
    }
}
