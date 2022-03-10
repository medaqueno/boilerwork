#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\BC\Application;

use App\Core\BC\Domain\User;
// use App\Core\BC\Domain\UserRepository;
use App\Core\BC\Infra\Persistence\UserRedisRepository;
use Kernel\Application\CommandHandlerInterface;
use Kernel\Application\CommandInterface;

final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function __construct()
    {
        $this->userRepository = new UserRedisRepository();
    }

    public function handle(CommandInterface $command): void
    {

        $aggregate = User::register(
            email: $command->email,
            username: $command->username,
        );

        // var_dump($aggregate->toArray());
        go(function () {
            $exists = $this->userRepository->ofId(10);


            // var_dump($exists);
        });




        // $this->userRepository->add($aggregate);
    }
}
