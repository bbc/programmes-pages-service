<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

trait AggregatedEpisodesCountMethodsTrait
{
    public function getAggregatedEpisodesCount(): int
    {
        return $this->aggregatedEpisodesCount;
    }

    public function setAggregatedEpisodesCount(int $aggregatedEpisodesCount)
    {
        $this->aggregatedEpisodesCount = $aggregatedEpisodesCount;
    }
}
