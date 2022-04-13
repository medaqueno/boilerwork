#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Container;

use Psr\Container\ContainerInterface;
use Illuminate\Container\Container as ContainerImplementation;
use App\Core\BC\Domain\UserRepository;
use App\Core\BC\Infra\Persistence\UserInMemoryRepository;
use App\Core\BC\Infra\Persistence\UserPostgreSQLRepository;
use App\Core\BC\Infra\Persistence\UserRedisRepository;

/**
 * Dependency Injection Container
 * Still deciding which one to use finally or if create manually a Service Container
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
        // Hay que estudiar esta versión para añadir singletons al contenedor en cuanto a rendimiento y problemas de datos
        $UserInMemoryRepository = UserInMemoryRepository::getInstance();
        $this->container->instance(UserRepository::class, $UserInMemoryRepository);
        */
        // $UserPostgreSQLRepository = UserPostgreSQLRepository::getInstance();
        // $this->container->instance(UserRepository::class, $UserPostgreSQLRepository);
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
