#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use App\Core\ExampleBoundedContext\Domain\ExampleDomain\ExampleRepository;
use App\Core\ExampleBoundedContext\Infra\Persistence\ExamplePostgreSQLRepository;

final class ContainerBindingsProvider
{
    private array $services = [
        [ExampleRepository::class, 'bind', ExamplePostgreSQLRepository::class],
        //
        // Default bindings
        [\Boilerwork\System\Messaging\MessagingClientInterface::class, 'bind', \Boilerwork\System\Messaging\Adapters\KafkaMessageClientAdapter::class],
        [\Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLWritesPool::class, 'singleton', null], // Start PostgreSQL Connection Pools Read and Writes to be used by services
        [\Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLReadsPool::class, 'singleton', null], // Start PostgreSQL Connection Pools Read and Writes to be used by services
    ];

    public function __construct()
    {
        foreach ($this->services as $service) {
            call_user_func([$this, $service[1]], $service[0], $service[2]);
        }
    }

    private function bind($abstract, $concrete): void
    {
        container()->bind($abstract, $concrete);
    }

    private function singleton($concrete): void
    {
        container()->bind($concrete, function () use ($concrete) {
            return $concrete::getInstance();
        });
    }
}
