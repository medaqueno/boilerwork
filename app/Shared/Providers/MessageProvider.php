#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use App\Core\BC\UI\Ports\Message\ReceiveMessagePort;

final class MessageProvider
{
    private array $subscriptions = [
        // ['queueName', 'exchangeNullable', ConsumerClass::class],
        ['queue' => 'test-message/withExchange', 'exchange' => 'exchangeTest', 'target' => ReceiveMessagePort::class],
        ['queue' => 'test-message/onlyQueue', 'exchange' => null, 'target' => ReceiveMessagePort::class],
    ];

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }
}
