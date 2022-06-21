#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Message;

use Boilerwork\UI\AbstractMessagePort;
use PhpAmqpLib\Message\AMQPMessage;

final class ReceiveMessagePort extends AbstractMessagePort
{
    public function __invoke(AMQPMessage $msg): void
    {
        // echo "AQUI ESTAMOS EN " . __CLASS__ . "\n";
        // var_dump($msg->getRoutingKey());
        // var_dump($msg->getQueue());
        // var_dump($msg->getBody());
    }
}
