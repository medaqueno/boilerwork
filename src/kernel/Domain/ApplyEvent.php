#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use Kernel\Domain\DomainEvent;

trait ApplyEvent
{
    /**
     * Execute when<eventClassName> methods automatically
     **/
    protected function apply(DomainEvent $event)
    {
        $method = 'apply' .  $this->getName($event::class);
        $this->$method($event);
    }

    /**
     * Extract Class name without namespace
     **/
    private function getName(string $event): string
    {
        if ($pos = strrpos($event, '\\')) {
            $eventName = substr($event, $pos + 1);
        } else {
            $eventName = $event;
        }

        return $eventName;
    }
}
