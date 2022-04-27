#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use Kernel\Events\EventPublisher;

final class EventsSubscribeProvider
{
    private array $subscribers = [
        // \App\Core\BC\Infra\Messaging\ExampleSubscriber::class,
        \App\Core\BC\Infra\Projections\UserEmailUniqueness::class,
    ];

    public function __construct(private EventPublisher $eventPublisher)
    {
        foreach ($this->subscribers as $aSubscriber) {
            $eventPublisher->subscribe($aSubscriber);
        }
    }
}
