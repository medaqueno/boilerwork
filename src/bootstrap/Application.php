#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Bootstrap;

use App\Shared\Providers\EventsSubscribeProvider;
use Kernel\Events\EventPublisher;
use Kernel\Helpers\Environments;
use Kernel\Helpers\Singleton;
use Kernel\System\Container\Container;
use Psr\Container\ContainerInterface;

/**
 * Load basic dependencies and configs in the App
 *
 **/
final class Application
{
    use Singleton;

    public const VERSION = '0.1.0';

    private string $environment;

    private ContainerInterface $container;

    private function __construct()
    {
        $this->environment = $_ENV['APP_ENV'] ?? Environments::DEVELOPMENT;

        // Init Dependency Injection Container
        $this->container = new Container;

        new EventsSubscribeProvider(EventPublisher::getInstance());
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    public function container(): ContainerInterface
    {
        return $this->container;
    }
}
