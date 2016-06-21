<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class Contributor
{
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
     * @var string|null
     */
    private $musicBrainzId;


    public function __construct(
        Pid $pid,
        string $type,
        string $name,
        string $musicBrainzId = null
    ) {
        $this->pid = $pid;
        $this->type = $type;
        $this->name = $name;
        $this->musicBrainzId = $musicBrainzId;
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

    /**
     * @return string|null
     */
    public function getMusicBrainzId()
    {
        return $this->musicBrainzId;
    }
}
