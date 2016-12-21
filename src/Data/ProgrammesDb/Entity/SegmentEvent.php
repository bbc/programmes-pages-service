<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\SegmentEventRepository")
 */
class SegmentEvent
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
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(nullable=false, onDelete="RESTRICT")
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="Segment", inversedBy="segmentEvents")
     * @ORM\JoinColumn(nullable=false, onDelete="RESTRICT")
     */
    private $segment;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=128, nullable=true)
     */
    private $title;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $offset;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isChapter = false;

    public function __construct(string $pid, Version $version, Segment $segment)
    {
        $this->pid = $pid;
        $this->version = $version;
        $this->segment = $segment;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid)
    {
        $this->pid = $pid;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version)
    {
        $this->version = $version;
    }

    public function getSegment(): Segment
    {
        return $this->segment;
    }

    public function setSegment(Segment $segment)
    {
        $this->segment = $segment;
    }

    /**
     * @return string|null
     */
    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle(string $title = null)
    {
        $this->title = $title;
    }

    /**
     * @return int|null
     */
    public function getOffset()
    {
        return $this->offset;
    }

    public function setOffset(int $offset = null)
    {
        $this->offset = $offset;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition(int $position = null)
    {
        $this->position = $position;
    }

    public function getIsChapter(): bool
    {
        return $this->isChapter;
    }

    public function setIsChapter(bool $isChapter)
    {
        $this->isChapter = $isChapter;
    }
}
