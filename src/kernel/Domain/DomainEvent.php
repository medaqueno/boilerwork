#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

use Kernel\Domain\ValueObjects\Identity;

interface DomainEvent
{
    public function getAggregateId(): Identity;
}
