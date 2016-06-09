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
    private $aggregatedBroadcastsCount;

    /**
     * @var int
     */
    private $availableClipsCount;

    /**
     * @var int
     */
    private $availableGalleriesCount;


    public function __construct(
        int $dbId,
        Pid $pid,
        string $title,
        string $searchTitle,
        string $shortSynopsis,
        string $longestSynopsis,
        Image $image,
        Image $alternativeImage = null,
        int $promotionsCount,
        int $relatedLinksCount,
        bool $hasSupportingContent,
        bool $isStreamable,
        string $mediaType,
        int $aggregatedBroadcastsCount,
        int $availableClipsCount,
        int $availableGalleriesCount,
        Programme $parent = null,
        int $position = null,
        MasterBrand $masterBrand = null,
        array $genres = [],
        array $formats = [],
        PartialDate $releaseDate = null,
        int $duration = null,
        DateTimeImmutable $streamableFrom = null,
        DateTimeImmutable $streamableUntil = null
    ) {
        parent::__construct(
            $dbId,
            $pid,
            $title,
            $searchTitle,
            $shortSynopsis,
            $longestSynopsis,
            $image,
            $alternativeImage,
            $promotionsCount,
            $relatedLinksCount,
            $hasSupportingContent,
            $isStreamable,
            $mediaType,
            $parent,
            $position,
            $masterBrand,
            $genres,
            $formats,
            $releaseDate,
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
