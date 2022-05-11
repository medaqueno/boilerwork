#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Clients;

interface MessagingInterface
{
    /**
     * Example Use:
     * $messagingClient = new MessagingClient();
     *
     * // Using Exchange. Queue may be omitted, because queues are binded in Broker Admin manually or by subscribers
     * $messagingClient->publish(message: 'this is an example message', queue: 'test-mqtt/withExchange', exchange: 'exchangeTest');
     * or better:
     * $messagingClient->publish(message: 'this is an example message', queue: null, exchange: 'exchangeTest');
     *
     * // Using only queues
     * $messagingClient->publish(message: 'this is an example message', queue: 'test-mqtt/onlyQueue');
     **/
    public function publish(string $message, string $queue): void;

    public function subscribe(string $queue, string $exchange = null, callable $fn): void;
}
