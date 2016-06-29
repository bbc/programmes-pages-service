<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;

class Segment
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
    private $title;

    /**
     * @var Synopses
     */
    private $synopses;

    /**
     * @var int|null
     */
    private $duration;

    public function __construct(
        Pid $pid,
        string $type,
        string $title,
        Synopses $synopses,
        int $duration = null
    ) {
        $this->pid = $pid;
        $this->type = $type;
        $this->title = $title;
        $this->synopses = $synopses;
        $this->duration = $duration;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function getTitle(): string
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
}
