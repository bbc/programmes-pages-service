<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;

abstract class ProgrammeContainer extends Programme
{
    /**
     * @var int
     */
    protected $aggregatedBroadcastsCount;

    /**
     * @var int
     */
    protected $aggregatedEpisodesCount;

    /**
     * @var int
     */
    protected $availableClipsCount;

    /**
     * @var int
     */
    protected $availableEpisodesCount;

    /**
     * @var int
     */
    protected $availableGalleriesCount;

    /**
     * @var string
     */
    protected $isPodcastable;

    /**
     * @var int|null
     */
    protected $expectedChildCount;

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
        array $relatedLinks = [],
        int $expectedChildCount = null
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
            $parent,
            $releaseDate,
            $position,
            $masterBrand,
            $genres,
            $formats,
            $relatedLinks
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
