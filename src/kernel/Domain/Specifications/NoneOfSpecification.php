#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain\Specifications;

class NoneOfSpecification extends Specification
{
    public function __construct(private readonly Specification ...$specifications)
    {
    }

    /**
     * {@inheritdoc}
     */
    public function isSatisfiedBy($object): bool
    {
        foreach ($this->specifications as $specification) {
            if ($specification->isSatisfiedBy($object)) {
                return false;
            }
        }

        return true;
    }
}
