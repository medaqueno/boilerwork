#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use Boilerwork\System\Messaging\MessageProviderInterface;

final class MessageProvider implements MessageProviderInterface
{
    // Topics should first created in Kafka or produced before subscribe to them.
    private array $subscriptions = [
        ['topic' => 'testTopic1', 'target' => \App\Core\BC\UI\Ports\Messaging\ExampleReceiveMessagePort::class],
    ];

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }
}
