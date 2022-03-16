#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Events;

use Ds\Vector;
use Kernel\Helpers\Singleton;
use RuntimeException;
use Throwable;

final class EventPublisher
{
    use Singleton;

    private function __construct(
        private Vector $subscribers = new Vector()
    ) {
    }

    public function subscribe($eventSubscriber): void
    {
        if (class_exists($eventSubscriber)) {
            $this->subscribers->push($eventSubscriber);
        } else {
            error(sprintf('%s: %s class does not exist', __class__, $eventSubscriber), RuntimeException::class);
        }
    }

    public function publish($event): void
    {
        foreach ($this->subscribers as $subscriber) {
            $class = new $subscriber;
            if ($class->isSubscribedTo() === $event::class) {

                go(function () use ($class, $event) {
                    try {
                        $class->handle($event);
                        // (app()->container()->get($class))->handle($event);
                    } catch (RuntimeException $e) {
                        error($e->getMessage(), RuntimeException::class);
                    } catch (Throwable $e) {
                        error($e->getMessage());
                    }
                });
            }
            unset($class);
        }
    }
}
