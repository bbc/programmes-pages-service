<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

trait AggregatedBroadcastsCountMethodsTrait
{
    public function getAggregatedBroadcastsCount(): int
    {
        return $this->aggregatedBroadcastsCount;
    }

    public function setAggregatedBroadcastsCount(int $aggregatedBroadcastsCount)
    {
        $this->aggregatedBroadcastsCount = $aggregatedBroadcastsCount;
    }
}
