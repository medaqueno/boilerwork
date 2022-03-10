#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use Kernel\Domain\AggregateRoot;

final class User extends AggregateRoot
{
    private function __construct(private string $email, private string $username, private int $status)
    {
    }

    public static function register($email, $username): self
    {
        $entity = new static(
            email: $email,
            username: $username,
            status: 1,
        );

        return $entity;
    }
}
