#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Bootstrap;

use App\Shared\Providers\EventsSubscribeProvider;
use Boilerwork\Helpers\Environments;
use Boilerwork\Helpers\Singleton;
use App\Shared\Providers\ContainerBindingsProvider;
use define;

/**
 * Load basic dependencies and configs in the App
 *
 **/
final class Application
{
    use Singleton;

    public const VERSION = '0.1.0';

    private string $environment;

    public function __construct()
    {
        // Init Environments ?
        $this->environment = $_ENV['APP_ENV'] ?? Environments::DEVELOPMENT;

        $this->initPaths();
        $this->initTimeZone();
        $this->initErrorReportingLevel();

        // Init Basic Providers
        new ContainerBindingsProvider();
        new EventsSubscribeProvider();

        logger('hola');
    }

    private function initPaths(): void
    {
        define('APP_PATH', __DIR__ . '/../app');
        define('BASE_PATH', __DIR__ . '/..');
    }

    /**
     * Set Timezone
     **/
    private function initTimeZone(): void
    {
        date_default_timezone_set($_ENV['APP_TIMEZONE']);
    }

    private function initErrorReportingLevel(): void
    {
        error_reporting(E_ALL);
        ini_set('display_errors', ($_ENV['APP_DEBUG'] === 'true') ?? false);
    }

    public function getEnvironment(): string
    {
        return $this->environment;
    }
}
