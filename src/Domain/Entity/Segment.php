<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
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

    /**
     * @var array|null
     */
    private $contributions;

    /**
     * @var int
     */
    private $contributionsCount;

    public function __construct(
        int $dbId,
        Pid $pid,
        string $type,
        Synopses $synopses,
        int $contributionsCount,
        string $title = null,
        int $duration = null,
        array $contributions = null
    ) {
        $this->dbId = $dbId;
        $this->pid = $pid;
        $this->type = $type;
        $this->title = $title;
        $this->synopses = $synopses;
        $this->contributionsCount = $contributionsCount;
        $this->duration = $duration;
        $this->contributions = $contributions;
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

    /**
     * @return string|null
     */
    public function getTitle()
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

    /**
     * @return int
     */
    public function getContributionsCount(): int
    {
        return $this->contributionsCount;
    }

    /**
     * This will return an array of contributions which will mean that
     * it might be called like $segmentEvent->getSegment()->getContributions()
     * which is a couple of layers of nested getters however as all of our known use cases require
     * us to fetch contributions with segment events this is something we will probably always need,
     * so it's more preferable to have the getter than to have the client match the contributions
     * and the segment events. Throws an exception if the contributions were not fetched
     */
    public function getContributions(): array
    {
        if (is_null($this->contributions)) {
            throw new DataNotFetchedException(
                'Could not get Contributions of Segment "' . $this->pid . '" as it was not fetched'
            );
        }

        return $this->contributions;
    }
}
