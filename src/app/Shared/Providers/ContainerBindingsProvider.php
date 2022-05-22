#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

final class ContainerBindingsProvider
{
    private array $services = [
        [\App\Core\BC\Domain\UserRepository::class, 'bind', \App\Core\BC\Infra\Persistence\UserPostgreSQLRepository::class],
        [\Boilerwork\System\Clients\PostgreSQLWritesPool::class, 'singleton', null], // Start PostgreSQL Connection Pools Read and Writes to be used by services
        [\Boilerwork\System\Clients\PostgreSQLReadsPool::class, 'singleton', null], // Start PostgreSQL Connection Pools Read and Writes to be used by services
        [\Boilerwork\System\Clients\RedisPool::class, 'singleton', null],   // Start Redis Connection Pool to be used by services
        [\Boilerwork\System\Clients\MessagePool::class, 'singleton', null], // Start Message Connection Pool to be used by services
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
