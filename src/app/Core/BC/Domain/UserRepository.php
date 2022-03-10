#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain;

interface UserRepository
{
    public function add(User $user): void;

    public function remove(User $user): void;

    public function ofId(int $userId): mixed;
}
