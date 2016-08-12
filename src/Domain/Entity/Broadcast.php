<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Broadcast
{
    /** @var string */
    private $pid;

    /** @var Service $service */
    private $service;

    /** @var string */
    private $startDate;

    /** @var string */
    private $start;

    /** @var string */
    private $end;

    /** @var int */
    private $duration = 0;

    /** @var bool */
    private $isBlanked = false;

    /** @var bool */
    private $isRepeat = false;

    public function __construct(
        string $pid,
        Service $service,
        \DateTime $start,
        \DateTime $end,
        int $duration,
        bool $isBlanked,
        bool $isRepeat
    ) {
        $this->pid = $pid;
        $this->service = $service;
        $this->startDate = $start->format("Y-m-d");
        $this->start = $start->format(DATE_ISO8601);
        $this->end = $end->format(DATE_ISO8601);
        $this->duration = $duration;
        $this->isBlanked = $isBlanked;
        $this->isRepeat = $isRepeat;
    }

    public function getPid()
    {
        return 'pid';
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function getStartDate(): string
    {
        return $this->startDate;
    }

    public function getStart(): string
    {
        return $this->start;
    }

    public function getEnd(): string
    {
        return $this->end;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function isBlanked(): bool
    {
        return $this->isBlanked;
    }

    public function isRepeat(): bool
    {
        return $this->isRepeat;
    }
}
