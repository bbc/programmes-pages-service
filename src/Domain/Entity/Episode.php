<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;

class Episode extends ProgrammeItem
{
    /** @var int */
    private $aggregatedBroadcastsCount;

    /** @var int */
    private $availableClipsCount;

    /** @var int */
    private $availableGalleriesCount;

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
        string $mediaType,
        int $segmentEventCount,
        int $aggregatedBroadcastsCount,
        int $availableClipsCount,
        int $availableGalleriesCount,
        Options $options,
        ?Programme $parent = null,
        ?int $position = null,
        ?MasterBrand $masterBrand = null,
        ?array $genres = null,
        ?array $formats = null,
        ?DateTimeImmutable $firstBroadcastDate = null,
        ?PartialDate $releaseDate = null,
        ?int $duration = null,
        ?DateTimeImmutable $streamableFrom = null,
        ?DateTimeImmutable $streamableUntil = null
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
            $mediaType,
            $segmentEventCount,
            $options,
            $parent,
            $position,
            $masterBrand,
            $genres,
            $formats,
            $firstBroadcastDate,
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
