#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Mqtt;

use Boilerwork\UI\AbstractMQTTPort;
use PhpAmqpLib\Message\AMQPMessage;

final class ReceiveMqttPort extends AbstractMQTTPort
{
    public function __invoke(AMQPMessage $msg): void
    {
        echo "AQUI ESTAMOS EN " . __CLASS__ . "\n";
        var_dump($msg->getRoutingKey());
        var_dump($msg->getExchange());
        var_dump($msg->getBody());
    }
}
