#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use Kernel\Domain\RecordsEvents;
use Kernel\Domain\ValueObjects\Identity;

interface UserRepository
{
    /**
     *  Add Events to Persistence from Aggregate
     **/
    public function add(RecordsEvents $aggregate): void;

    /**
     *  Retrieve Reconstituted Aggregate by its Event Stream in persistence where aggregateId = X
     **/
    public function get(Identity $aggregateId): RecordsEvents;
}
