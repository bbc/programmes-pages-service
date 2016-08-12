<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class Contribution
{
    /**
     * @var Pid
     */
    private $pid;

    /**
     * @var Contributor
     */
    private $contributor;

    /**
     * @var string
     */
    private $creditRole;

    /**
     * @var int|null
     */
    private $position;

    /**
     * @var string|null
     */
    private $characterName;


    public function __construct(
        Pid $pid,
        Contributor $contributor,
        string $creditRole,
        int $position = null,
        string $characterName = null
    ) {
        $this->pid = $pid;
        $this->contributor = $contributor;
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

    public function getCreditRole(): string
    {
        return $this->creditRole;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return string|null
     */
    public function getCharacterName()
    {
        return $this->characterName;
    }
}
