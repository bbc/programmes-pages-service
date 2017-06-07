<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

/**
 * @ORM\MappedSuperclass()
 */
abstract class ProgrammeItem extends Programme
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $embeddable = false;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $mediaType = MediaTypeEnum::UNKNOWN;

    /**
     * @var PartialDate|null
     *
     * @ORM\Column(type="date_partial", nullable=true)
     */
    private $releaseDate;

    /**
     * @var Version|null
     * @ORM\OneToOne(targetEntity="Version", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $streamableVersion;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $streamableFrom;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $streamableUntil;

    /**
     * @var Version|null
     * @ORM\OneToOne(targetEntity="Version", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $downloadableVersion;

    /**
     * @var array
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $downloadableMediaSets;

    /**
     * Duration - taken from the streamable version
     *
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): void
    {
        if (!in_array($mediaType, MediaTypeEnum::validValues())) {
            throw new InvalidArgumentException(sprintf(
                'Called setMediaType with an invalid value. Expected one of %s but got "%s"',
                '"' . implode('", "', MediaTypeEnum::validValues()) . '"',
                $mediaType
            ));
        }

        $this->mediaType = $mediaType;
    }

    public function getReleaseDate(): ?PartialDate
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(?PartialDate $releaseDate): void
    {
        $this->releaseDate = $releaseDate;
    }

    public function getStreamableVersion(): ?Version
    {
        return $this->streamableVersion;
    }

    public function setStreamableVersion(?Version $streamableVersion): void
    {
        $this->streamableVersion = $streamableVersion;
    }

    public function getStreamableFrom(): ?DateTime
    {
        return $this->streamableFrom;
    }

    public function setStreamableFrom(?DateTime $streamableFrom): void
    {
        $this->streamableFrom = $streamableFrom;
    }

    public function getStreamableUntil(): ?DateTime
    {
        return $this->streamableUntil;
    }

    public function setStreamableUntil(?DateTime $streamableUntil): void
    {
        $this->streamableUntil = $streamableUntil;
    }

    public function getDownloadableVersion(): ?Version
    {
        return $this->downloadableVersion;
    }

    public function setDownloadableVersion(?Version $downloadableVersion): void
    {
        $this->downloadableVersion = $downloadableVersion;
    }

    public function getDownloadableMediaSets(): ?array
    {
        return $this->downloadableMediaSets;
    }

    public function setDownloadableMediaSets(?array $downloadableMediaSets): void
    {
        $this->downloadableMediaSets = $downloadableMediaSets;
    }

    public function isEmbeddable(): bool
    {
        return $this->embeddable;
    }

    public function setEmbeddable(bool $embeddable): void
    {
        $this->embeddable = $embeddable;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }

    public function getSegmentEventCount(): int
    {
        return $this->segmentEventCount;
    }

    public function setSegmentEventCount(int $segmentEventCount): void
    {
        $this->segmentEventCount = $segmentEventCount;
    }
}
