<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class MusicSegment extends Segment
{
    /**
     * @var string|null
     */
    private $musicRecordId;

    /**
     * @var string|null
     */
    private $releaseTitle;

    /**
     * @var string|null
     */
    private $catalogueNumber;

    /**
     * @var string|null
     */
    private $recordLabel;

    /**
     * @var string|null
     */
    private $publisher;

    /**
     * @var string|null
     */
    private $trackNumber;

    /**
     * @var string|null
     */
    private $trackSide;

    /**
     * @var string|null
     */
    private $sourceMedia;

    /**
     * @var string|null
     */
    private $musicCode;

    /**
     * @var string|null
     */
    private $recordingDate;

    public function __construct(
        int $dbId,
        Pid $pid,
        string $type,
        string $title,
        Synopses $synopses,
        int $duration = null,
        string $musicRecordId = null,
        string $releaseTitle = null,
        string $catalogueNumber = null,
        string $recordLabel = null,
        string $publisher = null,
        string $trackNumber = null,
        string $trackSide = null,
        string $sourceMedia = null,
        string $musicCode = null,
        string $recordingDate = null
    ) {
        parent::__construct($dbId, $pid, $type, $title, $synopses, $duration);

        $this->musicRecordId = $musicRecordId;
        $this->releaseTitle = $releaseTitle;
        $this->catalogueNumber = $catalogueNumber;
        $this->recordLabel = $recordLabel;
        $this->publisher = $publisher;
        $this->trackNumber = $trackNumber;
        $this->trackSide = $trackSide;
        $this->sourceMedia = $sourceMedia;
        $this->musicCode = $musicCode;
        $this->recordingDate = $recordingDate;
    }

    /**
     * @return string|null
     */
    public function getMusicRecordId()
    {
        return $this->musicRecordId;
    }

    /**
     * @return string|null
     */
    public function getReleaseTitle()
    {
        return $this->releaseTitle;
    }

    /**
     * @return string|null
     */
    public function getCatalogueNumber()
    {
        return $this->catalogueNumber;
    }

    /**
     * @return string|null
     */
    public function getRecordLabel()
    {
        return $this->recordLabel;
    }

    /**
     * @return string|null
     */
    public function getPublisher()
    {
        return $this->publisher;
    }

    /**
     * @return string|null
     */
    public function getTrackNumber()
    {
        return $this->trackNumber;
    }

    /**
     * @return string|null
     */
    public function getTrackSide()
    {
        return $this->trackSide;
    }

    /**
     * @return string|null
     */
    public function getSourceMedia()
    {
        return $this->sourceMedia;
    }

    /**
     * @return string|null
     */
    public function getMusicCode()
    {
        return $this->musicCode;
    }

    /**
     * @return string|null
     */
    public function getRecordingDate()
    {
        return $this->recordingDate;
    }
}
