<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AggregatedEpisodesCountTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $aggregatedEpisodesCount = 0;

    public function getAggregatedEpisodesCount(): int
    {
        return $this->aggregatedEpisodesCount;
    }

    public function setAggregatedEpisodesCount(int $aggregatedEpisodesCount)
    {
        $this->aggregatedEpisodesCount = $aggregatedEpisodesCount;
    }
}
