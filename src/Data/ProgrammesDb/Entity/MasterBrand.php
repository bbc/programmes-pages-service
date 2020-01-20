<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 *
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\MasterBrandRepository")
 */
class MasterBrand
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;

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
     * @ORM\Column(type="string", length=64, nullable=false, unique=true)
     */
    private $mid;

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
    private $name;

    /**
     * @var Network|null
     *
     * @ORM\ManyToOne(targetEntity="Network", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $network;

    /**
     * @var Image|null
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $image;

    /**
     * @var Version|null
     *
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $competitionWarning;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $colour;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlKey;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    private $streamableInPlayspace = false;

    public function __construct(string $mid, string $pid, string $name)
    {
        $this->mid = $mid;
        $this->pid = $pid;
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getMid(): string
    {
        return $this->mid;
    }

    public function setMid(string $mid): void
    {
        $this->mid = $mid;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid): void
    {
        // TODO Validate PID

        $this->pid = $pid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(?Network $network): void
    {
        $this->network = $network;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): void
    {
        $this->image = $image;
    }

    public function getCompetitionWarning(): ?Version
    {
        return $this->competitionWarning;
    }

    public function setCompetitionWarning(?Version $competitionWarning): void
    {
        $this->competitionWarning = $competitionWarning;
    }

    public function getColour(): ?string
    {
        return $this->colour;
    }

    public function setColour(?string $colour)
    {
        $this->colour = $colour;
    }

    public function getUrlKey(): ?string
    {
        return $this->urlKey;
    }

    public function setUrlKey(?string $urlKey): void
    {
        $this->urlKey = $urlKey;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }

    public function getStreamableInPlayspace(): bool
    {
        return $this->streamableInPlayspace;
    }

    public function setStreamableInPlayspace(bool $streamableInPlayspace)
    {
        $this->streamableInPlayspace = $streamableInPlayspace;
    }
}
