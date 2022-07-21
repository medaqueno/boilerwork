#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Infra\Persistence;

use App\Core\ExampleBoundedContext\Domain\ExampleDomain\ExampleRepository;
use Boilerwork\Infra\Persistence\Adapters\PostgreSQL\PostgreSQLEventStoreAdapter;

final class ExamplePostgreSQLRepository extends PostgreSQLEventStoreAdapter implements ExampleRepository
{
}
