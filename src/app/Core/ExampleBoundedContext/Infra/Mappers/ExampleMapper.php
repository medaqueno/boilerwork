#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Infra\Mappers;

use DateTimeImmutable;

final class ExampleMapper
{
    public static function map(array $item): array
    {
        return [
            "id" => $item['aggregate_id'],
            "name" => $item['name'],
            "region" => $item['region'],
            "created_at" => (new DateTimeImmutable($item['created_at']))->format(DateTimeImmutable::ATOM),
            "updated_at" => (new DateTimeImmutable($item['updated_at']))->format(DateTimeImmutable::ATOM),
        ];
    }
}
