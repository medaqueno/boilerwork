#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Application;

final class CommandBus
{
    // private float $time;

    public function __construct()
    {
        // $this->time = microtime(true);
    }

    /**
     * Dispatch the command
     */
    public function handle(CommandInterface $command): void
    {
        // With DI
        $commandHandler = app()->container()->get(get_class($command) . 'Handler');

        // Without DI, should add ..$args
        // $class = get_class($command) . 'Handler';
        // $commandHandler = new $class;

        // Used for logging
        // $commandName = $this->getCommandName($command);

        // Execute commandHandler
        try {
            call_user_func([$commandHandler, 'handle'], $command);

            // Log all mutations in data made with commands
            /*
                logger(json_encode(
                    [
                        'commandName' => $commandName,
                        'payload' => $command,
                        'time' => $this->time,
                    ]
                ));
                */
        } catch (\Exception $e) {
            /*
                logger(json_encode(
                    [
                        'commandName' => $commandName,
                        'payload' => $command,
                        'time' => $this->time,
                        'exception' => $e,
                    ]
                ));
                */
            throw $e;
        }
    }

    private function getCommandName(CommandInterface $command): string
    {
        $commandClass = get_class($command);

        if ($pos = strrpos($commandClass, '\\')) {
            $commandName = substr($commandClass, $pos + 1);
        } else {
            $commandName = $commandClass;
        }

        return $commandName;
    }
}
