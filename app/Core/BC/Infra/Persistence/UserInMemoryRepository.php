#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\UserRepository;
use Boilerwork\Infra\Persistence\Adapters\InMemory\InMemoryEventStoreAdapter;

final class UserInMemoryRepository extends InMemoryEventStoreAdapter implements UserRepository
{
}
