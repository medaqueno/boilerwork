#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Bootstrap;

use League\Container\Container;
use League\Container\ReflectionContainer;
use Swoole\Runtime;
use App\Shared\Providers\BindContainerProvider;

/**
 * Load basic dependencies in the App
 *
 **/
final class Application
{
    static public $app;

    public const VERSION = '0.1.0';

    protected readonly string $environment;

    public Container $containerBuilder;

    private function __construct()
    {
        $this->environment = $_ENV['APP_ENV'] ?? 'DEV';

        // Enable Hooks to allow Courotines to work automatically
        // https://openswoole.com/docs/modules/swoole-runtime-flags
        Runtime::enableCoroutine(true, \SWOOLE_HOOK_ALL);

        $this->launchDependencyInjectionContainer();
    }

    static function getInstance(): self
    {
        if (!self::$app) {
            self::$app = new self();
        }

        return self::$app;
    }

    /**
     * Dependency Injection Container
     * https://container.thephpleague.com/
     */
    private function launchDependencyInjectionContainer(): void
    {
        // Autowire
        $this->containerBuilder = new Container;

        $this->containerBuilder
            // register the reflection container as a delegate to enable auto wiring
            ->delegate(new ReflectionContainer)
            // Add service Provider
            ->addServiceProvider(new BindContainerProvider);
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
