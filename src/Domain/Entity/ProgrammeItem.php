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

    /** @var int */
    private $segmentEventCount;

    /** @var PartialDate|null */
    private $releaseDate;

    /** @var int|null */
    private $duration;

    /** @var DateTimeImmutable|null */
    private $streamableFrom;

    /** @var DateTimeImmutable|null */
    private $streamableUntil;

    /** @var string[] */
    private $downloadableMediaSets;

    /** @var bool */
    private $isExternallyEmbeddable;

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
        int $aggregatedGalleriesCount,
        bool $isExternallyEmbeddable,
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
        ?DateTimeImmutable $streamableUntil = null,
        array $downloadableMediaSets = []
    ) {
        if (!in_array($mediaType, MediaTypeEnum::validValues())) {
            throw new InvalidArgumentException(sprintf(
                'Tried to create a ProgrammeItem with an invalid MediaType. Expected one of %s but got "%s"',
                '"' . implode('", "', MediaTypeEnum::validValues()) . '"',
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
            $aggregatedGalleriesCount,
            $options,
            $parent,
            $position,
            $masterBrand,
            $genres,
            $formats,
            $firstBroadcastDate
        );

        $this->mediaType = $mediaType;
        $this->segmentEventCount = $segmentEventCount;
        $this->isExternallyEmbeddable = $isExternallyEmbeddable;
        $this->releaseDate = $releaseDate;
        $this->duration = $duration;
        $this->streamableFrom = $streamableFrom;
        $this->streamableUntil = $streamableUntil;
        $this->downloadableMediaSets = $downloadableMediaSets;
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

    public function isAudio(): bool
    {
        return ($this->mediaType === MediaTypeEnum::AUDIO);
    }

    public function isVideo(): bool
    {
        return ($this->mediaType === MediaTypeEnum::VIDEO);
    }

    public function getDownloadableMediaSets(): array
    {
        return $this->downloadableMediaSets;
    }

    public function isDownloadable(): bool
    {
        return !empty($this->downloadableMediaSets);
    }

    public function hasFutureAvailability(): bool
    {
        // We don't need to check if the streamableFrom date is in the future or if the streamableUntil
        // date is in the past because these fields get cleared when something stops being streamable
        return !$this->isStreamable() && $this->getStreamableFrom();
    }

    public function isExternallyEmbeddable(): bool
    {
        return $this->isExternallyEmbeddable;
    }

    public function isPlayable(): bool
    {
        return $this->isStreamable();
    }
}
