<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use InvalidArgumentException;

class Version
{
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
     * @var VersionType[]
     */
    private $versionTypes;

    public function __construct(
        Pid $pid,
        ProgrammeItem $programmeItem,
        int $duration = null,
        string $guidanceWarningCodes = null,
        bool $hasCompetitionWarning = false,
        array $versionTypes = []
    ) {
        foreach ($versionTypes as $vt) {
            if (!$vt instanceof VersionType) {
                throw new InvalidArgumentException(
                    '$versionTypes must be an array containing only instance of "BBC\ProgrammesPagesService\Domain\Entity\VersionType". ' .
                    'Found instance of "' . (is_object($vt) ? get_class($vt) : gettype($vt)) . '"'
                );
            }
        }

        $this->pid = $pid;
        $this->duration = $duration;
        $this->guidanceWarningCodes = $guidanceWarningCodes;
        $this->hasCompetitionWarning = $hasCompetitionWarning;
        $this->programmeItem = $programmeItem;
        $this->versionTypes = $versionTypes;
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
        return $this->versionTypes;
    }
}
