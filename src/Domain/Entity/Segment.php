<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class Segment
{
    /**
     * @var int
     */
    private $dbId;

    /**
     * @var Pid
     */
    private $pid;

    /**
     * @var string
     */
    private $type;

    /**
     * @var string
     */
    private $title;

    /**
     * @var Synopses
     */
    private $synopses;

    /**
     * @var int|null
     */
    private $duration;

    public function __construct(
        int $dbId,
        Pid $pid,
        string $type,
        string $title,
        Synopses $synopses,
        int $duration = null
    ) {
        $this->dbId = $dbId;
        $this->pid = $pid;
        $this->type = $type;
        $this->title = $title;
        $this->synopses = $synopses;
        $this->duration = $duration;
    }

    /**
     * Database ID. Yes, this is a leaky abstraction as Database Ids are
     * implementation details of how we're storing data, rather than anything
     * intrinsic to a PIPS entity. However if we keep it pure then when we look
     * up things like "All segment events that belong to a Segment" then we
     * have to use the Segment PID as the key, which requires a join to the
     * Segment table. This join can be avoided if we already know the Foreign
     * Key value on the SegmentEvent table (i.e. the Segment ID field).
     * Removing these joins shall result in faster DB queries which is more
     * important than keeping a pure Domain model.
     */
    public function getDbId(): int
    {
        return $this->dbId;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSynopses(): Synopses
    {
        return $this->synopses;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }
}
