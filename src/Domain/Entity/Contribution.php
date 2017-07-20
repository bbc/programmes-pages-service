<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGroup;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedSegment;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use InvalidArgumentException;

class Contribution
{
    /** @var Pid */
    private $pid;

    /** @var Contributor */
    private $contributor;

    /** @var Group|Programme|Segment|Version */
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
        $contributedTo,
        string $creditRole,
        ?int $position = null,
        ?string $characterName = null
    ) {
        if (!($contributedTo instanceof Programme || $contributedTo instanceof Segment || $contributedTo instanceof Version || $contributedTo instanceof Group)) {
            throw new InvalidArgumentException(sprintf(
                'Expected $contributedTo to be an instance of "%s", "%s", "%s" or "%s". Found instance of "%s"',
                Group::CLASS,
                Programme::CLASS,
                Segment::CLASS,
                Version::CLASS,
                (is_object($contributedTo) ? get_class($contributedTo) : gettype($contributedTo))
            ));
        }

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
     * @return Group|Programme|Segment|Version
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
