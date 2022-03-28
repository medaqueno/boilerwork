#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use Kernel\Infra\Persistence\EventStore;

interface UserRepository extends EventStore
{
}
