<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository")
 * @ORM\Table(indexes={
 *   @ORM\Index(name="version_streamable_idx", columns={"streamable"})
 * })
 */
class Version
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", length=15, nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $guidanceWarningCodes;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $competitionWarning = false;

    /**
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     * @ORM\JoinColumn(nullable=false, onDelete="RESTRICT")
     */
    private $programmeItem;

    /**
     * @ORM\ManyToMany(targetEntity="VersionType", cascade="persist")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $versionTypes;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    private $segmentEventCount = 0;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false, options={"default" = 0})
     */
    private $contributionsCount = 0;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    private $streamable = false;

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
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    private $downloadable = false;

    /**
     * @var array
     *
     * @ORM\Column(type="simple_array", nullable=true)
     */
    private $downloadableMediaSets;

    /**
     * Used for joins. Cannot be queried, so there is no getter/setter.
     * @ORM\OneToMany(targetEntity="Broadcast", mappedBy="version")
     */
    private $broadcasts;

    /**
     * Used for joins. Cannot be queried, so there is no getter/setter.
     * @ORM\OneToMany(targetEntity="Contribution", mappedBy="contributionToVersion")
     */
    private $contributions;

    public function __construct(string $pid, ProgrammeItem $programmeItem)
    {
        $this->pid = $pid;
        $this->versionTypes = new ArrayCollection();
        $this->broadcasts = new ArrayCollection();
        $this->contributions = new ArrayCollection();
        $this->setProgrammeItem($programmeItem);
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

    public function getStreamable(): bool
    {
        return $this->streamable;
    }

    public function setStreamable(bool $streamable): void
    {
        $this->streamable = $streamable;
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

    public function getDownloadable(): bool
    {
        return $this->downloadable;
    }

    public function setDownloadable(bool $downloadable): void
    {
        $this->downloadable = $downloadable;
    }

    public function getDownloadableMediaSets(): ?array
    {
        return $this->downloadableMediaSets;
    }

    public function setDownloadableMediaSets(?array $downloadableMediaSets): void
    {
        $this->downloadableMediaSets = $downloadableMediaSets;
    }

    public function getDuration(): ?int
    {
        return $this->duration;
    }

    public function setDuration(?int $duration): void
    {
        $this->duration = $duration;
    }

    public function getGuidanceWarningCodes(): ?string
    {
        return $this->guidanceWarningCodes;
    }

    public function setGuidanceWarningCodes(?string $guidanceWarningCodes): void
    {
        $this->guidanceWarningCodes = $guidanceWarningCodes;
    }

    public function getCompetitionWarning(): bool
    {
        return $this->competitionWarning;
    }

    public function setCompetitionWarning(bool $competitionWarning): void
    {
        $this->competitionWarning = $competitionWarning;
    }

    public function getContributionsCount(): int
    {
        return $this->contributionsCount;
    }

    public function setContributionsCount(int $contributionsCount): void
    {
        $this->contributionsCount = $contributionsCount;
    }

    public function getProgrammeItem(): ProgrammeItem
    {
        return $this->programmeItem;
    }

    public function setProgrammeItem(ProgrammeItem $programmeItem): void
    {
        $this->programmeItem = $programmeItem;
    }

    public function getVersionTypes(): DoctrineCollection
    {
        return $this->versionTypes;
    }

    public function setVersionTypes(DoctrineCollection $versionTypes): void
    {
        $this->versionTypes = $versionTypes;
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
