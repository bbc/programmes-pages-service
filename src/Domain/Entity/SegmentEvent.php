<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class SegmentEvent
{
    /** @var string */
    private $pid;

    /** @var Version */
    private $version;

    /** @var Segment */
    private $segment;

    /** @var string|null */
    private $title;

    /** @var Synopses */
    private $synopses;

    /** @var bool */
    private $isChapter = false;

    /** @var int|null */
    private $offset;

    /** @var int|null */
    private $position;

    public function __construct(
        Pid $pid,
        Version $version,
        Segment $segment,
        Synopses $synopses,
        ?string $title = null,
        bool $isChapter = false,
        ?int $offset = null,
        ?int $position = null
    ) {
        $this->pid = $pid;
        $this->version = $version;
        $this->segment = $segment;
        $this->synopses = $synopses;
        $this->title = $title;
        $this->isChapter = $isChapter;
        $this->offset = $offset;
        $this->position = $position;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getVersion(): Version
    {
        if ($this->version instanceof UnfetchedVersion) {
            throw new DataNotFetchedException('Could not get Version of SegmentEvent "' . $this->pid . '" as it was not fetched');
        }

        return $this->version;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getSegment(): Segment
    {
        if ($this->segment instanceof UnfetchedSegment) {
            throw new DataNotFetchedException('Could not get Segment of SegmentEvent "' . $this->pid . '" as it was not fetched');
        }

        return $this->segment;
    }

    public function getSynopses(): Synopses
    {
        return $this->synopses;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function isChapter(): bool
    {
        return $this->isChapter;
    }

    public function getOffset(): ?int
    {
        return $this->offset;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }
}
