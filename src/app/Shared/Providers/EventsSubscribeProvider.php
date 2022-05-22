#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

final class EventsSubscribeProvider
{
    private array $subscribers = [
        // \App\Core\BC\Infra\Messaging\ExampleSubscriber::class,
        \App\Core\BC\Infra\Projections\UserEmailUniqueness::class,
    ];

    public function __construct()
    {
        foreach ($this->subscribers as $aSubscriber) {
            eventsPublisher()->subscribe($aSubscriber);
        }
    }
}
