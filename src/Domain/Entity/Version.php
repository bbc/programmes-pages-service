<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
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
     * @var ProgrammeItem
     */
    private $programmeItem;

    /**
     * @var VersionType[]|null
     */
    private $versionTypes;

    public function __construct(
        int $dbId,
        Pid $pid,
        ProgrammeItem $programmeItem,
        int $duration = null,
        string $guidanceWarningCodes = null,
        bool $hasCompetitionWarning = false,
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
        $this->duration = $duration;
        $this->guidanceWarningCodes = $guidanceWarningCodes;
        $this->hasCompetitionWarning = $hasCompetitionWarning;
        $this->programmeItem = $programmeItem;
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

    public function getPid(): string
    {
        return $this->pid;
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
     * @return ProgrammeItem|null
     */
    public function getProgrammeItem()
    {
        return $this->programmeItem;
    }

    /**
     * @return VersionType[]
     */
    public function getVersionTypes(): array
    {
        if (is_null($this->versionTypes)) {
            throw new DataNotFetchedException('Could not get VersionTypes of Version "' . $this->pid . '" as they were not fetched');
        }
        return $this->versionTypes;
    }
}
