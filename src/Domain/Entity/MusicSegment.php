<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class MusicSegment extends Segment
{
    /** @var string|null */
    private $musicRecordId;

    /** @var string|null */
    private $releaseTitle;

    /** @var string|null */
    private $catalogueNumber;

    /** @var string|null */
    private $recordLabel;

    /** @var string|null */
    private $publisher;

    /** @var string|null */
    private $trackNumber;

    /** @var string|null */
    private $trackSide;

    /** @var string|null */
    private $sourceMedia;

    /** @var string|null */
    private $musicCode;

    /** @var string|null */
    private $recordingDate;

    public function __construct(
        int $dbId,
        Pid $pid,
        string $type,
        Synopses $synopses,
        int $contributionsCount,
        string $title = null,
        int $duration = null,
        ?array $contributions = null,
        ?string $musicRecordId = null,
        ?string $releaseTitle = null,
        ?string $catalogueNumber = null,
        ?string $recordLabel = null,
        ?string $publisher = null,
        ?string $trackNumber = null,
        ?string $trackSide = null,
        ?string $sourceMedia = null,
        ?string $musicCode = null,
        ?string $recordingDate = null
    ) {
        parent::__construct($dbId, $pid, $type, $synopses, $contributionsCount, $title, $duration, $contributions);

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

    public function getMusicRecordId(): ?string
    {
        return $this->musicRecordId;
    }

    public function getReleaseTitle(): ?string
    {
        return $this->releaseTitle;
    }

    public function getCatalogueNumber(): ?string
    {
        return $this->catalogueNumber;
    }

    public function getRecordLabel(): ?string
    {
        return $this->recordLabel;
    }

    public function getPublisher(): ?string
    {
        return $this->publisher;
    }

    public function getTrackNumber(): ?string
    {
        return $this->trackNumber;
    }

    public function getTrackSide(): ?string
    {
        return $this->trackSide;
    }

    public function getSourceMedia(): ?string
    {
        return $this->sourceMedia;
    }

    public function getMusicCode(): ?string
    {
        return $this->musicCode;
    }

    public function getRecordingDate(): ?string
    {
        return $this->recordingDate;
    }
}
