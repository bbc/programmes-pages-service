<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="segment_pid_idx", columns={"pid"}),
 * })
 * @ORM\Entity()
 */
class Segment
{
    use TimestampableEntity;
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
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $musicRecordId;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $releaseTitle;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $catalogueNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $recordLabel;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $publisher;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $trackNumber;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $trackSide;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $sourceMedia;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $musicCode;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $recordingDate;

    public function __construct(string $pid, string $type)
    {
        $this->pid = $pid;
        $this->type = $type;
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

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
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
    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration(int $duration = null)
    {
        $this->duration = $duration;
    }

    /**
     * @return string|null
     */
    public function getMusicRecordId()
    {
        return $this->musicRecordId;
    }

    public function setMusicRecordId(string $musicRecordId = null)
    {
        $this->musicRecordId = $musicRecordId;
    }

    /**
     * @return string|null
     */
    public function getReleaseTitle()
    {
        return $this->releaseTitle;
    }

    public function setReleaseTitle(string $releaseTitle = null)
    {
        $this->releaseTitle = $releaseTitle;
    }

    /**
     * @return string|null
     */
    public function getCatalogueNumber()
    {
        return $this->catalogueNumber;
    }

    public function setCatalogueNumber(string $catalogueNumber = null)
    {
        $this->catalogueNumber = $catalogueNumber;
    }

    /**
     * @return string|null
     */
    public function getRecordLabel()
    {
        return $this->recordLabel;
    }

    public function setRecordLabel(string $recordLabel = null)
    {
        $this->recordLabel = $recordLabel;
    }

    /**
     * @return string|null
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    public function setPublisher(string $publisher = null)
    {
        $this->publisher = $publisher;
    }

    /**
     * @return string|null
     */
    public function getTrackNumber()
    {
        return $this->trackNumber;
    }

    public function setTrackNumber(string $trackNumber = null)
    {
        $this->trackNumber = $trackNumber;
    }

    /**
     * @return string|null
     */
    public function getTrackSide()
    {
        return $this->trackSide;
    }

    public function setTrackSide(string $trackSide = null)
    {
        $this->trackSide = $trackSide;
    }

    /**
     * @return string|null
     */
    public function getSourceMedia()
    {
        return $this->sourceMedia;
    }

    public function setSourceMedia(string $sourceMedia = null)
    {
        $this->sourceMedia = $sourceMedia;
    }

    /**
     * @return string|null
     */
    public function getMusicCode()
    {
        return $this->musicCode;
    }

    public function setMusicCode(string $musicCode = null)
    {
        $this->musicCode = $musicCode;
    }

    /**
     * @return string|null
     */
    public function getRecordingDate()
    {
        return $this->recordingDate;
    }

    public function setRecordingDate(string $recordingDate = null)
    {
        $this->recordingDate = $recordingDate;
    }
}
