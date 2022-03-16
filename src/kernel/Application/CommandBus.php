#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Application;

final class CommandBus
{
    private float $time;

    public function __construct()
    {
        $this->time = microtime(true);
    }

    /**
     * Handle Commands Asynchronously as a Job
     */
    /*
    public function async(CommandInterface $command, ?string $jobClass = GenericJob::class)
    {
        // try {
        //     $jobClass::dispatch($command);
        // } catch (\Throwable $th) {
        //     throw new JobDispatchingException($jobClass, $th);
        // }
    }
    */

    /**
     * Dispatch the command
     */
    public function handle(CommandInterface $command): void
    {
        // go(function () use ($command, $args) {

        $commandHandler = app()->container()->get(get_class($command) . 'Handler');
        // $commandName = $this->getCommandName($command); // Used for logging

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
        // });
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
