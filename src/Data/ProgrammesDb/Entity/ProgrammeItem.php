<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\MediaTypeEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
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
     * @var DateTime
     *
     * @ORM\Column(type="string", nullable=false)
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
     * @ORM\OneToOne(targetEntity="Version")
     * @ORM\JoinColumn(onDelete="SET NULL")
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
     * @ORM\OneToOne(targetEntity="Version")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $downloadableVersion;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="string", nullable=true)
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


    /**
     * @param string $mediaType
     * @throws InvalidArgumentException
     */
    public function setMediaType($mediaType)
    {
        if (!in_array($mediaType, [MediaTypeEnum::AUDIO, MediaTypeEnum::VIDEO, MediaTypeEnum::UNKNOWN])) {
            throw new InvalidArgumentException(sprintf(
                'Called setMediaType with an invalid value. Expected one of "%s", "%s" or "%s" but got "%s"',
                MediaTypeEnum::AUDIO,
                MediaTypeEnum::VIDEO,
                MediaTypeEnum::UNKNOWN,
                $mediaType
            ));
        }

        $this->mediaType = $mediaType;
    }

    /**
     * @return PartialDate|null
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
    }

    public function setReleaseDate(PartialDate $releaseDate = null)
    {
        $this->releaseDate = $releaseDate;
    }

    /**
     * @return Version|null
     */
    public function getStreamableVersion()
    {
        return $this->streamableVersion;
    }

    public function setStreamableVersion(Version $streamableVersion = null)
    {
        $this->streamableVersion = $streamableVersion;
    }

    /**
     * @return DateTime|null
     */
    public function getStreamableFrom()
    {
        return $this->streamableFrom;
    }

    public function setStreamableFrom(DateTime $streamableFrom = null)
    {
        $this->streamableFrom = $streamableFrom;
    }

    /**
     * @return DateTime|null
     */
    public function getStreamableUntil()
    {
        return $this->streamableUntil;
    }

    public function setStreamableUntil(DateTime $streamableUntil = null)
    {
        $this->streamableUntil = $streamableUntil;
    }

    /**
     * @return Version|null
     */
    public function getDownloadableVersion()
    {
        return $this->downloadableVersion;
    }

    public function setDownloadableVersion(Version $downloadableVersion = null)
    {
        $this->downloadableVersion = $downloadableVersion;
    }

    /**
     * @return string|null
     */
    public function getDownloadableMediaSets()
    {
        return $this->downloadableMediaSets;
    }

    public function setDownloadableMediaSets(string $downloadableMediaSets = null)
    {
        $this->downloadableMediaSets = $downloadableMediaSets;
    }

    public function isEmbeddable(): bool
    {
        return $this->embeddable;
    }

    public function setEmbeddable(bool $embeddable)
    {
        $this->embeddable = $embeddable;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @param int|null $duration
     */
    public function setDuration($duration)
    {
        $this->duration = $duration;
    }
}
