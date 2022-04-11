#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use App\Core\BC\Domain\UserRepository;
use App\Core\BC\Infra\Persistence\UserInMemoryRepository;
use App\Core\BC\Infra\Persistence\UserRedisRepository;
use Kernel\Infra\Persistence\EventStore;
use Kernel\Infra\Persistence\InMemoryEventStore;
use League\Container\ServiceProvider\AbstractServiceProvider;

/*
* For PHP LEAGUE DEPENDENCY INJECTION. Still thinking about to use it or not

NOW IT IS NOT IN USE
*/

final class BindContainerProvider extends AbstractServiceProvider
{
    /**
     * The provides method is a way to let the container
     * know that a service is provided by this service
     * provider. Every service that is registered via
     * this service provider must have an alias added
     * to this array or it will be ignored.
     */
    public function provides(string $id): bool
    {
        // Interfaces
        $services = [
            UserRepository::class,
        ];

        return in_array($id, $services);
    }

    /**
     * This is where the magic happens, within the method you can
     * access the container and register or retrieve anything
     * that you need to, but remember, every alias registered
     * within this method must be declared in the `$provides` array.
     */
    public function register(): void
    {
        // Example
        // $this->getContainer()->add(\App\Core\BC\UI\Controllers\Interface::class, \App\Core\BC\UI\Controllers\ConcreteClass::class);

        $this->getContainer()->add(UserRepository::class, UserRedisRepository::class);
        // $this->getContainer()->add(EventStore::class, InMemoryEventStore::class);
        // $this->getContainer()->add(UserRepository::class, UserInMemoryRepository::class);
    }
}
