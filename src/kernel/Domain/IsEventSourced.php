#!/usr/bin/env php
<?php

declare(strict_types=1);

namespace Kernel\Domain;

/**
 * An AggregateRoot, that can be reconstituted from an AggregateHistory.
 */
interface IsEventSourced
{
    /**
     * @param AggregateHistory $aggregateHistory
     * @return RecordsEvents
     */
    public static function reconstituteFrom(AggregateHistory $aggregateHistory);

    /**
     * @return IdentifiesAggregate
     */
    // @todo do we need this here?
    //public function getAggregateId();
}
