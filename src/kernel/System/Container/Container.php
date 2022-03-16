#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Container;

use Psr\Container\ContainerInterface;
// use League\Container\Container as ContainerImplementation;
// use League\Container\ReflectionContainer;
// use App\Shared\Providers\BindContainerProvider;
use Illuminate\Container\Container as ContainerImplementation;
use App\Core\BC\Domain\UserRepository;
use App\Core\BC\Infra\Persistence\UserRedisRepository;


/**
 * Dependency Injection Container
 * Still deciding which one to use finally
 *
 * https://container.thephpleague.com/
 * https://laravel.com/api/9.x/Illuminate/Container/Container.html
 */
final class Container implements ContainerInterface
{
    private ContainerInterface $container;

    public function __construct()
    {
        $this->container = new ContainerImplementation;

        /*
        // League Implementation
        $this->container
            // register the reflection container as a delegate to enable auto wiring
            ->delegate(new ReflectionContainer())
            // Add service Provider
            ->addServiceProvider(new BindContainerProvider);*/

        // Illuminate Implementation
        $this->container->bind(UserRepository::class, UserRedisRepository::class);
    }

    public function get(string $id): mixed
    {
        return $this->container->get($id);
    }

    public function has(string $id): bool
    {
        return $this->container->has($id);
    }
}
