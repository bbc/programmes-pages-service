<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

trait AggregatedGalleriesCountMethodsTrait
{
    public function getAggregatedGalleriesCount(): int
    {
        return $this->aggregatedGalleriesCount;
    }

    public function setAggregatedGalleriesCount(int $aggregatedGalleriesCount): void
    {
        $this->aggregatedGalleriesCount = $aggregatedGalleriesCount;
    }
}
