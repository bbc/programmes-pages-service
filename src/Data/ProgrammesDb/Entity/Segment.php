<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentRepository")
 */
class Segment
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;
    use Traits\SynopsesTrait;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=15, nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $title;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    // The following properties should only apply to Music Segments

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $musicRecordId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $releaseTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $catalogueNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recordLabel;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $publisher;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $trackNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=8, nullable=true)
     */
    private $trackSide;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=64, nullable=true)
     */
    private $sourceMedia;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=10, nullable=true)
     */
    private $musicCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $recordingDate;

    /**
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="contributionToSegment")
     */
    private $contributions;

    /**
     * Used for joins. Cannot be queried, so there is no getter/setter.
     * @ORM\OneToMany(targetEntity="SegmentEvent", mappedBy="segment")
     */
    private $segmentEvents;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    private $contributionsCount = 0;

    public function __construct(string $pid, string $type)
    {
        $this->pid = $pid;
        $this->type = $type;
        $this->contributions = new ArrayCollection();
        $this->segmentEvents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid): void
    {
        $this->pid = $pid;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(?string $title): void
    {
        $this->title = $title;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }

    public function getMusicRecordId(): ?string
    {
        return $this->musicRecordId;
    }

    public function setMusicRecordId(?string $musicRecordId): void
    {
        $this->musicRecordId = $musicRecordId;
    }

    public function getReleaseTitle(): ?string
    {
        return $this->releaseTitle;
    }

    public function setReleaseTitle(?string $releaseTitle): void
    {
        $this->releaseTitle = $releaseTitle;
    }

    public function getCatalogueNumber(): ?string
    {
        return $this->catalogueNumber;
    }

    public function setCatalogueNumber(?string $catalogueNumber): void
    {
        $this->catalogueNumber = $catalogueNumber;
    }

    public function getRecordLabel(): ?string
    {
        return $this->recordLabel;
    }

    public function setRecordLabel(?string $recordLabel): void
    {
        $this->recordLabel = $recordLabel;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function setPublisher(?string $publisher): void
    {
        $this->publisher = $publisher;
    }

    public function getTrackNumber(): ?string
    {
        return $this->trackNumber;
    }

    public function setTrackNumber(?string $trackNumber): void
    {
        $this->trackNumber = $trackNumber;
    }

    public function getTrackSide(): ?string
    {
        return $this->trackSide;
    }

    public function setTrackSide(?string $trackSide): void
    {
        $this->trackSide = $trackSide;
    }

    public function getSourceMedia(): ?string
    {
        return $this->sourceMedia;
    }

    public function setSourceMedia(?string $sourceMedia): void
    {
        $this->sourceMedia = $sourceMedia;
    }

    public function getMusicCode(): ?string
    {
        return $this->musicCode;
    }

    public function setMusicCode(?string $musicCode): void
    {
        $this->musicCode = $musicCode;
    }

    public function getRecordingDate(): ?string
    {
        return $this->recordingDate;
    }

    public function setRecordingDate(?string $recordingDate): void
    {
        $this->recordingDate = $recordingDate;
    }

    public function getContributionsCount(): int
    {
        return $this->contributionsCount;
    }

    public function setContributionsCount(int $contributionsCount): void
    {
        $this->contributionsCount = $contributionsCount;
    }
}
