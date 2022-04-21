#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Clients;

use PhpAmqpLib\Message\AMQPMessage;

/**
 * Communicate with MQTT Broker
 *
 * Subscribing must be done in MQTTProvider
 **/
class MessagingClient implements MessagingInterface
{
    private readonly MQTTPool $pool;

    public function __construct()
    {
        $this->pool = app()->container()->get(MQTTPool::class);
    }

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
    public function publish(string $message, ?string $queue = null, ?string $exchange = null): void
    {
        $connection = $this->pool->getUpstreamConn();
        $channel = $connection->channel();

        $msg = new AMQPMessage($message);

        if ($exchange) {
            $channel->exchange_declare($exchange, 'fanout', false, false, false);
            $channel->basic_publish($msg, $exchange);
        } else {
            // Send only to queue
            // Create Queue if doesn't exist. It may be optional, depending on the messaging strategy selected
            $channel->queue_declare($queue, false, false, false, false);
            $channel->basic_publish($msg, '', $queue);
        }

        $channel->close();
        $this->pool->putUpstreamConn($connection);
    }

    /***
     * Subscribing must be done in MQTTProvider
     *
     * @use \App\Shared\Providers\MQTTProvider
     **/
    public function subscribe(string $queue, string $exchange = null, callable $fn): void
    {
        throw new \Exception("Use MQTTProvider to subscribe to messaging");
    }
}