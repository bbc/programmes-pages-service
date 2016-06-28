<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

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
