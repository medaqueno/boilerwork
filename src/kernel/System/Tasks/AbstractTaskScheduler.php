#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\System\Tasks;

use Carbon\Carbon;
use Swoole\Coroutine;

abstract class AbstractTaskScheduler
{
    /**
     * @description Runs every minute
     * Ex: [ExampleTask::class, [self::INTERVAL_EVERY_MINUTE, null]]
     **/
    public const INTERVAL_EVERY_MINUTE = 'everyMinute';

    /**
     * @description Runs every hour at the specified minute
     * Ex: 01 or 56 [ExampleTask::class, [self::INTERVAL_HOURLY_AT_MINUTE, '08']]
     **/
    public const INTERVAL_HOURLY_AT_MINUTE = 'hourlyAtMinute';

    /**
     * @description Runs every hour
     * Ex: [ExampleTask::class, [self::INTERVAL_EVERY_HOUR, null]]
     **/
    public const INTERVAL_EVERY_HOUR = 'everyHour';

    /**
     * @description Runs every day at the specified hour
     * Ex: 03 or 21 [ExampleTask::class, [self::INTERVAL_DAILY_AT_HOUR, '04']]
     **/
    public const INTERVAL_DAILY_AT_HOUR = 'dailyAtHour';

    /**
     * @description Runs every day at the specified Time HH:ss
     * Ex: 23:03 or 2:23 [ExampleTask::class, [self::INTERVAL_DAILY_AT_TIME, '8:04']]
     **/
    public const INTERVAL_DAILY_AT_TIME = 'dailyAtTime';

    /**
     * @description Runs every week at specified day by its correlative number
     * Ex: between 1 (monday) and 7 (sunday) [ExampleTask::class, [self::INTERVAL_EVERY_DAY_OF_WEEK_ISO, '2']]
     **/
    public const INTERVAL_EVERY_DAY_OF_WEEK_ISO = 'everyDayOfWeekIso';

    protected array $tasks = [];

    private array $tasksToBeExecuted = [];

    public function run(): void
    {
        $this->checkTasks();
    }

    protected function checkTasks(): void
    {
        // Add to tasksToBeExecuted array if proceeds
        foreach ($this->tasks as $task) {

            // Check if Task if in time to be executed
            if ($this->shouldTrigger($task) === true) {
                // If it is not in task to be executed, add it
                if (!isset($this->tasksToBeExecuted[$task[0]])) {
                    $this->tasksToBeExecuted[$task[0]] = $task[0];
                }
            } else {
                // It is not time, if still exists in task to be executed, remove it
                if (array_key_exists($task[0], $this->tasksToBeExecuted) === true) {
                    unset($this->tasksToBeExecuted[$task[0]]);
                    reset($this->tasksToBeExecuted);
                }
            }
        }

        // Execute tasks in tasksToBeExecuted array
        foreach ($this->tasksToBeExecuted as $taskToExecute) {

            // Task is in time but has not been executed yet
            if ($taskToExecute !== 'executed') {

                go(function () use ($taskToExecute) {

                    $task = app()->containerBuilder->get($taskToExecute);

                    if ($task instanceof TaskInterface) {
                        $task->handle();
                    } else {
                        $message = 'Task ' . $taskToExecute . ' should implement TaskInterface';
                        logger($message);
                        throw new \RuntimeException($message);
                    }
                });

                $this->tasksToBeExecuted[$taskToExecute] = 'executed';
            }
        }
    }

    /**
     * Checks if it is the moment to execute a task.
     **/
    private function shouldTrigger(array $task): bool
    {
        $now = Carbon::now();

        $period = $task[1][0];
        $moment = $task[1][1] ?? null;

        return match ($period) {
            self::INTERVAL_HOURLY_AT_MINUTE => ($now->minute == $moment),
            self::INTERVAL_EVERY_MINUTE => ($now->second <= 30),
            self::INTERVAL_EVERY_HOUR => ($now->minute == 0),
            self::INTERVAL_DAILY_AT_HOUR => ($now->hour == $moment),
            self::INTERVAL_DAILY_AT_TIME => ($now->hour . ':' . $now->minute == $moment),
            self::INTERVAL_EVERY_DAY_OF_WEEK_ISO => ($now->dayOfWeekIso == $moment),
            default => false,
        };
    }
}
