#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

use Kernel\Domain\AggregateRoot;

final class User extends AggregateRoot
{
    private function __construct(
        protected int $id,
        private string $email,
        private string $username,
        private int $status
    ) {
    }

    public static function register(string $email, string $username): self
    {
        $entity = new static(
            id: 10,
            email: $email,
            username: $username,
            status: 1,
        );

        return $entity;
    }

    public function approveUser(): void
    {
    }

    public function toArray(): array
    {
        return [
            'id' => $this->id,
            'email' => $this->email,
            'username' => $this->username,
            'status' => $this->status,
        ];
    }
}
