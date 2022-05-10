#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use App\Core\BC\UI\Ports\Mqtt\ReceiveMqttPort;

final class MQTTProvider
{
    private array $subscriptions = [
        // ['queueName', 'exchangeNullable', ConsumerClass::class],
        ['queue' => 'test-mqtt/withExchange', 'exchange' => 'exchangeTest', 'target' => ReceiveMqttPort::class],
        ['queue' => 'test-mqtt/onlyQueue', 'exchange' => null, 'target' => ReceiveMqttPort::class],
    ];

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }
}
