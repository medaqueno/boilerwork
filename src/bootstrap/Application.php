#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Bootstrap;

use Swoole\Runtime;
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
        $this->environment = $_ENV['APP_ENV'] ?? 'DEV';

        // Enable Hooks to allow Courotines to work automatically
        // https://openswoole.com/docs/modules/swoole-runtime-flags
        !defined('SWOOLE_HOOK_FLAGS') && define('SWOOLE_HOOK_FLAGS', \SWOOLE_HOOK_ALL);
        Runtime::enableCoroutine(true, \SWOOLE_HOOK_ALL);

        $this->launchDependencyInjectionContainer();
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }

    private function launchDependencyInjectionContainer(): void
    {
        $this->container = new Container;
    }

    public function container(): ContainerInterface
    {
        return $this->container;
    }
}
