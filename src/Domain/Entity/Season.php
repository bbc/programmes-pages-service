<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use \DateTime;

class Season extends Group
{
    protected const TYPE = 'season';

    /* @var int */
    private $aggregatedBroadcastsCount;

    /* @var DateTime */
    private $endDate;

    /* @var DateTime */
    private $startDate;

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
        ?MasterBrand $masterBrand = null,
        ?DateTime $startDate = null,
        ?DateTime $endDate = null
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
        $this->startDate = $startDate;
        $this->endDate = $endDate;
    }

    public function getAggregatedBroadcastsCount(): int
    {
        return $this->aggregatedBroadcastsCount;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }
}
