<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;

abstract class ProgrammeContainer extends Programme
{
    /**
     * @var int
     */
    private $aggregatedBroadcastsCount;

    /**
     * @var int
     */
    private $aggregatedEpisodesCount;

    /**
     * @var int
     */
    private $availableClipsCount;

    /**
     * @var int
     */
    private $availableEpisodesCount;

    /**
     * @var int
     */
    private $availableGalleriesCount;

    /**
     * @var string
     */
    private $isPodcastable;

    /**
     * @var int|null
     */
    private $expectedChildCount;

    public function __construct(
        int $dbId,
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
        int $aggregatedBroadcastsCount,
        int $aggregatedEpisodesCount,
        int $availableClipsCount,
        int $availableEpisodesCount,
        int $availableGalleriesCount,
        bool $isPodcastable,
        Programme $parent = null,
        PartialDate $releaseDate = null,
        int $position = null,
        MasterBrand $masterBrand = null,
        array $genres = [],
        array $formats = [],
        int $expectedChildCount = null
    ) {
        parent::__construct(
            $dbId,
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
            $parent,
            $releaseDate,
            $position,
            $masterBrand,
            $genres,
            $formats
        );

        $this->aggregatedBroadcastsCount = $aggregatedBroadcastsCount;
        $this->aggregatedEpisodesCount = $aggregatedEpisodesCount;
        $this->availableClipsCount = $availableClipsCount;
        $this->availableEpisodesCount = $availableEpisodesCount;
        $this->availableGalleriesCount = $availableGalleriesCount;
        $this->isPodcastable = $isPodcastable;
        $this->expectedChildCount = $expectedChildCount;
    }

    public function getAggregatedBroadcastsCount(): int
    {
        return $this->aggregatedBroadcastsCount;
    }

    public function getAggregatedEpisodesCount(): int
    {
        return $this->aggregatedEpisodesCount;
    }

    public function getAvailableClipsCount(): int
    {
        return $this->availableClipsCount;
    }

    public function getAvailableEpisodesCount(): int
    {
        return $this->availableEpisodesCount;
    }

    public function getAvailableGalleriesCount(): int
    {
        return $this->availableGalleriesCount;
    }

    public function isPodcastable(): string
    {
        return $this->isPodcastable;
    }

    /**
     * @return int|null
     */
    public function getExpectedChildCount()
    {
        return $this->expectedChildCount;
    }
}
