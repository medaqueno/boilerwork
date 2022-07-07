#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Messaging;

use Boilerwork\System\Messaging\AbstractMessageSubscriberPort;
use Boilerwork\System\Messaging\Message;

final class ExampleReceiveMessagePort extends AbstractMessageSubscriberPort
{
    public function __invoke(Message $message): void
    {
        echo "Message Received in" . __CLASS__ . "\n";
        // var_dump($message);
        var_dump($message->getParsedPayload());
    }
}
