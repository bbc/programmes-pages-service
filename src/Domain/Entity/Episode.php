<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use DateTimeImmutable;

class Episode extends ProgrammeItem
{
    /**
     * @var int
     */
    protected $aggregatedBroadcastsCount;

    /**
     * @var int
     */
    protected $availableClipsCount;

    /**
     * @var int
     */
    protected $availableGalleriesCount;


    public function __construct(
        Pid $pid,
        string $title,
        string $searchTitle,
        string $shortSynopsis,
        string $longestSynopsis,
        Image $image,
        int $promotionsCount,
        int $relatedLinksCount,
        bool $hasSupportingContent,
        bool $isStreamable,
        string $mediaType,
        int $aggregatedBroadcastsCount,
        int $availableClipsCount,
        int $availableGalleriesCount,
        Programme $parent = null,
        PartialDate $releaseDate = null,
        int $position = null,
        MasterBrand $masterBrand = null,
        array $genres = [],
        array $formats = [],
        array $relatedLinks = [],
        int $duration = null,
        DateTimeImmutable $streamableFrom = null,
        DateTimeImmutable $streamableUntil = null
    ) {
        parent::__construct(
            $pid,
            $title,
            $searchTitle,
            $shortSynopsis,
            $longestSynopsis,
            $image,
            $promotionsCount,
            $relatedLinksCount,
            $hasSupportingContent,
            $isStreamable,
            $mediaType,
            $parent,
            $releaseDate,
            $position,
            $masterBrand,
            $genres,
            $formats,
            $relatedLinks,
            $duration,
            $streamableFrom,
            $streamableUntil
        );

        $this->aggregatedBroadcastsCount = $aggregatedBroadcastsCount;
        $this->availableClipsCount = $availableClipsCount;
        $this->availableGalleriesCount = $availableGalleriesCount;
    }

    public function getAggregatedBroadcastsCount(): int
    {
        return $this->aggregatedBroadcastsCount;
    }

    public function getAvailableClipsCount(): int
    {
        return $this->availableClipsCount;
    }

    public function getAvailableGalleriesCount(): int
    {
        return $this->availableGalleriesCount;
    }
}
