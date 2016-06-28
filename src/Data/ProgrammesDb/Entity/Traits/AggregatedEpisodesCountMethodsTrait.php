<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

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
