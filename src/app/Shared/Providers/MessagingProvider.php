#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use Boilerwork\System\Messaging\MessagingProviderInterface;

final class MessagingProvider implements MessagingProviderInterface
{
    // Topics should first created in Kafka or produced before subscribe to them.
    private array $subscriptions = [
        // ['topic' => 'example-was-created', 'target' => ExampleProjection::class],
    ];

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }
}
