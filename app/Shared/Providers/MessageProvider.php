#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

final class MessageProvider
{
    private array $subscriptions = [
        // ['topic' => 'topicTest', 'queue' => 'queueOrPartitionNullable', 'target' => ReceiveMessagePort::class],
    ];

    public function getSubscriptions(): array
    {
        return $this->subscriptions;
    }
}
