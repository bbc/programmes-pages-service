<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;
use InvalidArgumentException;

abstract class ProgrammeItem extends Programme
{
    /**
     * @var string
     */
    private $mediaType;

    /**
     * @var PartialDate|null
     */
    private $releaseDate;

    /**
     * @var int|null
     */
    private $duration;

    /**
     * @var DateTimeImmutable|null
     */
    private $streamableFrom;

    /**
     * @var DateTimeImmutable|null
     */
    private $streamableUntil;

    public function __construct(
        int $dbId,
        Pid $pid,
        string $title,
        string $searchTitle,
        Synopses $synopses,
        Image $image,
        int $promotionsCount,
        int $relatedLinksCount,
        bool $hasSupportingContent,
        bool $isStreamable,
        string $mediaType,
        Programme $parent = null,
        int $position = null,
        MasterBrand $masterBrand = null,
        array $genres = [],
        array $formats = [],
        PartialDate $releaseDate = null,
        DateTimeImmutable $firstBroadcastDate = null,
        int $duration = null,
        DateTimeImmutable $streamableFrom = null,
        DateTimeImmutable $streamableUntil = null
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
            $dbId,
            $pid,
            $title,
            $searchTitle,
            $synopses,
            $image,
            $promotionsCount,
            $relatedLinksCount,
            $hasSupportingContent,
            $isStreamable,
            $parent,
            $position,
            $masterBrand,
            $genres,
            $formats,
            $firstBroadcastDate
        );

        $this->mediaType = $mediaType;
        $this->releaseDate = $releaseDate;
        $this->duration = $duration;
        $this->streamableFrom = $streamableFrom;
        $this->streamableUntil = $streamableUntil;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    /**
     * @return PartialDate|null
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStreamableFrom()
    {
        return $this->streamableFrom;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStreamableUntil()
    {
        return $this->streamableUntil;
    }
}
