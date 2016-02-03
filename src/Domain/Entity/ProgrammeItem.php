<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use DateTimeImmutable;
use InvalidArgumentException;

abstract class ProgrammeItem extends Programme
{
    /**
     * @var string
     */
    protected $mediaType;

    /**
     * @var int|null
     */
    protected $duration;

    /**
     * @var DateTimeImmutable|null
     */
    protected $streamableFrom;

    /**
     * @var DateTimeImmutable|null
     */
    protected $streamableUntil;

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
        Programme $parent = null,
        PartialDate $releaseDate = null,
        int $position = null,
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
            $position
        );

        $this->mediaType = $mediaType;
        $this->duration = $duration;
        $this->streamableFrom = $streamableFrom;
        $this->streamableUntil = $streamableUntil;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
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
