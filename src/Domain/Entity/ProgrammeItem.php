<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;
use InvalidArgumentException;

abstract class ProgrammeItem extends Programme
{
    /** @var string */
    private $mediaType;

    /** @var int|null */
    private $segmentEventCount;

    /** @var PartialDate|null */
    private $releaseDate;

    /** @var int|null */
    private $duration;

    /** @var DateTimeImmutable|null */
    private $streamableFrom;

    /** @var DateTimeImmutable|null */
    private $streamableUntil;

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
        if (!in_array($mediaType, [MediaTypeEnum::AUDIO, MediaTypeEnum::VIDEO, MediaTypeEnum::UNKNOWN])) {
            throw new InvalidArgumentException(sprintf(
                'Tried to create a ProgrammeItem with an invalid MediaType. Expected one of "%s", "%s" or "%s" but got "%s"',
                MediaTypeEnum::AUDIO,
                MediaTypeEnum::VIDEO,
                MediaTypeEnum::UNKNOWN,
                $mediaType
            ));
        }

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
            $parent,
            $position,
            $masterBrand,
            $genres,
            $formats,
            $firstBroadcastDate
        );

        $this->mediaType = $mediaType;
        $this->segmentEventCount = $segmentEventCount;
        $this->releaseDate = $releaseDate;
        $this->duration = $duration;
        $this->streamableFrom = $streamableFrom;
        $this->streamableUntil = $streamableUntil;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function getSegmentEventCount(): int
    {
        return $this->segmentEventCount;
    }

    public function getReleaseDate(): ?PartialDate
    {
        return $this->releaseDate;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function getStreamableFrom(): ?DateTimeImmutable
    {
        return $this->streamableFrom;
    }

    public function getStreamableUntil(): ?DateTimeImmutable
    {
        return $this->streamableUntil;
    }
}
