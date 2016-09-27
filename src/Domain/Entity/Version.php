<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use DateTimeImmutable;
use InvalidArgumentException;

class Version
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
     * @var ProgrammeItem
     */
    private $programmeItem;

    /**
     * @var bool
     */
    private $isStreamable;

    /**
     * @var bool
     */
    private $isDownloadable;

    /**
     * @var int
     */
    private $segmentEventCount;

    /**
     * @var int|null
     */
    private $duration;

    /**
     * @var string|null
     */
    private $guidanceWarningCodes;

    /**
     * @var bool
     */
    private $hasCompetitionWarning;

    /**
     * @var DateTimeImmutable|null
     */
    private $streamableFrom;

    /**
     * @var DateTimeImmutable|null
     */
    private $streamableUntil;

    /**
     * @var VersionType[]|null
     */
    private $versionTypes;

    public function __construct(
        int $dbId,
        Pid $pid,
        ProgrammeItem $programmeItem,
        bool $isStreamable,
        bool $isDownloadable,
        int $segmentEventCount,
        int $duration = null,
        string $guidanceWarningCodes = null,
        bool $hasCompetitionWarning = false,
        DateTimeImmutable $streamableFrom = null,
        DateTimeImmutable $streamableUntil = null,
        array $versionTypes = null
    ) {
        if (is_array($versionTypes)) {
            foreach ($versionTypes as $vt) {
                if (!$vt instanceof VersionType) {
                    throw new InvalidArgumentException(
                        '$versionTypes must be an array containing only instance of "BBC\ProgrammesPagesService\Domain\Entity\VersionType". ' .
                        'Found instance of "' . (is_object($vt) ? get_class($vt) : gettype($vt)) . '"'
                    );
                }
            }
        }

        $this->dbId = $dbId;
        $this->pid = $pid;
        $this->programmeItem = $programmeItem;
        $this->isStreamable = $isStreamable;
        $this->isDownloadable = $isDownloadable;
        $this->segmentEventCount = $segmentEventCount;
        $this->duration = $duration;
        $this->guidanceWarningCodes = $guidanceWarningCodes;
        $this->hasCompetitionWarning = $hasCompetitionWarning;
        $this->streamableFrom = $streamableFrom;
        $this->streamableUntil = $streamableUntil;
        $this->versionTypes = $versionTypes;
    }

    /**
     * Database ID. Yes, this is a leaky abstraction as Database Ids are
     * implementation details of how we're storing data, rather than anything
     * intrinsic to a PIPS entity. However if we keep it pure then when we look
     * up things like "All broadcasts that belong to a Version" then we
     * have to use the Versio PID as the key, which requires a join to the
     * Version table. This join can be avoided if we already know the Foreign
     * Key value on the Broadcast table (i.e. the Version ID field).
     * Removing these joins shall result in faster DB queries which is more
     * important that keeping a pure Domain model.
     */
    public function getDbId(): int
    {
        return $this->dbId;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getProgrammeItem(): ProgrammeItem
    {
        return $this->programmeItem;
    }

    public function isStreamable(): bool
    {
        return $this->isStreamable;
    }

    public function isDownloadable(): bool
    {
        return $this->isDownloadable;
    }

    public function getSegmentEventCount(): int
    {
        return $this->segmentEventCount;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    /**
     * @return string|null
     */
    public function getGuidanceWarningCodes()
    {
        return $this->guidanceWarningCodes;
    }

    public function hasCompetitionWarning(): bool
    {
        return $this->hasCompetitionWarning;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStreamableFrom()
    {
        return $this->streamableFrom;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStreamableUntil()
    {
        return $this->streamableUntil;
    }

    /**
     * @return VersionType[]
     * @throws DataNotFetchedException
     */
    public function getVersionTypes(): array
    {
        if (is_null($this->versionTypes)) {
            throw new DataNotFetchedException('Could not get VersionTypes of Version "' . $this->pid . '" as they were not fetched');
        }
        return $this->versionTypes;
    }
}
