<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;

abstract class ProgrammeContainer extends Programme
{
    /** @var int */
    private $aggregatedBroadcastsCount;

    /** @var int */
    private $aggregatedEpisodesCount;

    /** @var int */
    private $availableClipsCount;

    /** @var int */
    private $availableEpisodesCount;

    /** @var bool */
    private $isPodcastable;

    /** @var int|null */
    private $expectedChildCount;

    public function __construct(
        array $dbAncestryIds,
        Pid $pid,
        string $title,
        string $searchTitle,
        Synopses $synopses,
        Image $image,
        int $promotionsCount,
        int $relatedLinksCount,
        bool $hasSupportingContent,
        bool $isStreamable,
        bool $isStreamableAlternate,
        int $contributionsCount,
        int $aggregatedBroadcastsCount,
        int $aggregatedEpisodesCount,
        int $availableClipsCount,
        int $availableEpisodesCount,
        int $aggregatedGalleriesCount,
        bool $isPodcastable,
        Options $options,
        ?Programme $parent = null,
        ?int $position = null,
        ?MasterBrand $masterBrand = null,
        ?array $genres = null,
        ?array $formats = null,
        ?DateTimeImmutable $firstBroadcastDate = null,
        ?int $expectedChildCount = null
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
            $hasSupportingContent,
            $isStreamable,
            $isStreamableAlternate,
            $contributionsCount,
            $aggregatedGalleriesCount,
            $options,
            $parent,
            $position,
            $masterBrand,
            $genres,
            $formats,
            $firstBroadcastDate
        );

        $this->aggregatedBroadcastsCount = $aggregatedBroadcastsCount;
        $this->aggregatedEpisodesCount = $aggregatedEpisodesCount;
        $this->availableClipsCount = $availableClipsCount;
        $this->availableEpisodesCount = $availableEpisodesCount;
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

    public function isPodcastable(): bool
    {
        return $this->isPodcastable;
    }

    public function getExpectedChildCount(): ?int
    {
        return $this->expectedChildCount;
    }

    public function isTlec(): bool
    {
        return $this->isTleo();
    }
}
