<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class Season extends Group
{
    protected $type = 'season';

    /* @var int */
    private $aggregatedBroadcastsCount;

    public function __construct(
        array $dbAncestryIds,
        Pid $pid,
        string $title,
        string $searchTitle,
        Synopses $synopses,
        Image $image,
        int $promotionsCount,
        int $relatedLinksCount,
        int $contributionsCount,
        Options $options,
        int $aggregatedBroadcastsCount,
        ?MasterBrand $masterBrand = null
    ) {
        parent::__construct(
            $dbAncestryIds,
            $pid,
            $title,
            $searchTitle,
            $synopses,
            $image,
            $promotionsCount,
            $relatedLinksCount,
            $contributionsCount,
            $options,
            $masterBrand
        );

        $this->aggregatedBroadcastsCount = $aggregatedBroadcastsCount;
    }

    public function getAggregatedBroadcastsCount(): int
    {
        return $this->aggregatedBroadcastsCount;
    }
}
