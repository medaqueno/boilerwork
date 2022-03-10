#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Shared\Providers;

use League\Container\ServiceProvider\AbstractServiceProvider;

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
        $services = [];

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
    }
}