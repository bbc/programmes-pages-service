<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGroup;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class Contribution
{
    /** @var Pid */
    private $pid;

    /** @var Contributor */
    private $contributor;

    /** @var ContributableToInterface */
    private $contributedTo;

    /** @var string */
    private $creditRole;

    /** @var int|null */
    private $position;

    /** @var string|null */
    private $characterName;

    public function __construct(
        Pid $pid,
        Contributor $contributor,
        ContributableToInterface $contributedTo,
        string $creditRole,
        ?int $position = null,
        ?string $characterName = null
    ) {
        $this->pid = $pid;
        $this->contributor = $contributor;
        $this->contributedTo = $contributedTo;
        $this->creditRole = $creditRole;
        $this->position = $position;
        $this->characterName = $characterName;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getContributor(): Contributor
    {
        return $this->contributor;
    }

    /**
     * @return ContributableToInterface
     * @throws DataNotFetchedException
     */
    public function getContributedTo()
    {
        if ($this->contributedTo instanceof UnfetchedProgramme || $this->contributedTo instanceof UnfetchedSegment || $this->contributedTo instanceof UnfetchedVersion || $this->contributedTo instanceof UnfetchedGroup) {
            throw new DataNotFetchedException(
                'Could not get ContributedTo of Contribution "' . $this->pid . '" as it was not fetched'
            );
        }

        return $this->contributedTo;
    }

    public function getCreditRole(): string
    {
        return $this->creditRole;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function getCharacterName(): ?string
    {
        return $this->characterName;
    }
}
