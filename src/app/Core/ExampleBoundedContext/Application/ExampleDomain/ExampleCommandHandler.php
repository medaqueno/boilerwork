#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace App\Core\ExampleBoundedContext\Application\ExampleDomain;

use App\Core\ExampleBoundedContext\Domain\ExampleDomain\Example;
use App\Core\ExampleBoundedContext\Domain\ExampleDomain\ExampleRepository;
use Boilerwork\Application\CommandHandlerInterface;
use Boilerwork\Application\CommandInterface;

/**
 * @see \App\Core\ExampleBoundedContext\Application\ExampleDomain\ExampleCommand
 **/
final class ExampleCommandHandler implements CommandHandlerInterface
{
    public function __construct(
        private ExampleRepository $exampleRepository
    ) {
    }

    public function handle(CommandInterface $command): void
    {
        $example = Example::create(
            exampleId: $command->exampleId,
            name: $command->name,
            region: $command->region,
        );

        $this->exampleRepository->append($example);

        eventsPublisher()->releaseEvents();
    }
}
