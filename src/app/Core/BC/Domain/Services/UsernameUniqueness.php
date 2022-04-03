#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Domain\Services;

use App\Core\BC\Domain\UserRepository;
use Kernel\Domain\Specifications\Specification;

final class UsernameUniqueness extends Specification
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function isSatisfiedBy($username): bool
    {
        return true;
    }
}
