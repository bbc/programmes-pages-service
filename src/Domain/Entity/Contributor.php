<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class Contributor
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
    private $name;

    /**
     * @var string
     */
    private $sortName;

    /**
     * @var string|null
     */
    private $musicBrainzId;


    public function __construct(
        int $dbId,
        Pid $pid,
        string $type,
        string $name,
        string $sortName = null,
        string $musicBrainzId = null
    ) {
        $this->dbId = $dbId;
        $this->pid = $pid;
        $this->type = $type;
        $this->name = $name;
        $this->sortName = $sortName;
        $this->musicBrainzId = $musicBrainzId;
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

    public function getSortName()
    {
        return $this->sortName;
    }

    /**
     * @return string|null
     */
    public function getMusicBrainzId()
    {
        return $this->musicBrainzId;
    }
}
