#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\UI\Ports\Message;

use Boilerwork\System\Messaging\AbstractMessagePort;
use Boilerwork\System\Messaging\Message;

final class ReceiveMessagePort extends AbstractMessagePort
{
    public function __invoke(Message $message): void
    {
        echo "Message Received in" . __CLASS__ . "\n";

        var_dump($message);
    }
}
