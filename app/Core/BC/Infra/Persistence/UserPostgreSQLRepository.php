#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Infra\Persistence;

use App\Core\BC\Domain\UserRepository;
use Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLEventStoreAdapter;

final class UserPostgreSQLRepository extends PostgreSQLEventStoreAdapter implements UserRepository
{
}
