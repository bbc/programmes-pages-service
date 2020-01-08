<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class Contributor
{
    /** @var int */
    private $dbId;

    /** @var Pid */
    private $pid;

    /** @var string */
    private $type;

    /** @var string */
    private $name;

    /** @var string|null */
    private $sortName;

    /** @var string|null */
    private $givenName;

    /** @var string|null */
    private $familyName;

    /** @var string|null */
    private $musicBrainzId;

    /** @var Thing|null */
    private $thing;

    public function __construct(
        int $dbId,
        Pid $pid,
        string $type,
        string $name,
        ?string $sortName = null,
        ?string $givenName = null,
        ?string $familyName = null,
        ?string $musicBrainzId = null,
        ?Thing $thing = null
    ) {
        $this->dbId = $dbId;
        $this->pid = $pid;
        $this->type = $type;
        $this->name = $name;
        $this->sortName = $sortName;
        $this->givenName = $givenName;
        $this->familyName = $familyName;
        $this->musicBrainzId = $musicBrainzId;
        $this->thing = $thing;
    }

    /**
     * Used to make foreign key queries without having to make a join
     * with the user-facing ID.
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

    public function getName(): string
    {
        return $this->name;
    }

    public function getSortName(): ?string
    {
        return $this->sortName;
    }

    public function getGivenName(): ?string
    {
        return $this->givenName;
    }

    public function getFamilyName(): ?string
    {
        return $this->familyName;
    }

    public function getMusicBrainzId(): ?string
    {
        return $this->musicBrainzId;
    }

    public function getThing(): ?Thing
    {
        return $this->thing;
    }
}
