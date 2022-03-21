#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use App\Core\BC\Domain\User;
use App\Core\BC\Domain\UserRepository;
use Kernel\Application\CommandHandlerInterface;
use Kernel\Application\CommandInterface;

/**
 * @uses App\Core\BC\Application\ExampleCommand
 **/
final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function __construct(private UserRepository $userRepository)
    {
    }

    public function handle(CommandInterface $command): void
    {
        $aggregate = User::register(
            email: $command->email,
            username: $command->username,
        );

        go(function () use ($aggregate) {

            // $this->userRepository->add($aggregate);

            $exists = $this->userRepository->ofId(10);
            // var_dump($exists);
        });
    }
}
