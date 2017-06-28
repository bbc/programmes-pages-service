<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use DateTimeImmutable;

class Broadcast
{
    /** @var Pid */
    private $pid;

    /** @var Version */
    private $version;

    /** @var ProgrammeItem */
    private $programmeItem;

    /** @var Service */
    private $service;

    /** @var DateTimeImmutable */
    private $startAt;

    /** @var DateTimeImmutable */
    private $endAt;

    /** @var int */
    private $duration;

    /** @var bool */
    private $isBlanked;

    /** @var bool */
    private $isRepeat;

    /** @var bool */
    private $isOnAir;

    public function __construct(
        Pid $pid,
        Version $version,
        ProgrammeItem $programmeItem,
        Service $service,
        DateTimeImmutable $startAt,
        DateTimeImmutable $endAt,
        int $duration,
        bool $isBlanked = false,
        bool $isRepeat = false
    ) {
        $this->pid = $pid;
        $this->version = $version;
        $this->programmeItem = $programmeItem;
        $this->service = $service;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->duration = $duration;
        $this->isBlanked = $isBlanked;
        $this->isRepeat = $isRepeat;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getVersion(): Version
    {
        if ($this->version instanceof UnfetchedVersion) {
            throw new DataNotFetchedException('Could not get Version of Broadcast "' . $this->pid . '" as it was not fetched');
        }

        return $this->version;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getProgrammeItem(): ProgrammeItem
    {
        if ($this->programmeItem instanceof UnfetchedProgrammeItem) {
            throw new DataNotFetchedException('Could not get ProgrammeItem of Broadcast "' . $this->pid . '" as it was not fetched');
        }

        return $this->programmeItem;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getService(): Service
    {
        if ($this->service instanceof UnfetchedService) {
            throw new DataNotFetchedException('Could not get Service of Broadcast "' . $this->pid . '" as it was not fetched');
        }

        return $this->service;
    }

    /**
     * When the broadcast started. This is inclusive, this is the very first
     * moment of the broadcast.
     * Given two broadcasts, the first will end at at 06:30:00 and the second
     * will start at 06:30:00.
     */
    public function getStartAt(): DateTimeImmutable
    {
        return $this->startAt;
    }

    /**
     * When the broadcast ended. This is exclusive, this is the very first
     * moment that the broadcast is no longer on.
     * Given two broadcasts, the first will end at at 06:30:00 and the second
     * will start at 06:30:00.
     */
    public function getEndAt(): DateTimeImmutable
    {
        return $this->endAt;
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

    public function isOnAir(): bool
    {
        if (!isset($this->isOnAir)) {
            $this->isOnAir = $this->isOnAirAt(ApplicationTime::getTime());
        }
        return $this->isOnAir;
    }

    /**
     * Returns true if the broadcast is on air at a given date.
     * Broadcast starts are inclusive and ends are exclusive.
     * Given two broadcasts, the first will end at at 06:30:00 and the second
     * will start at 06:30:00.
     */
    public function isOnAirAt(DateTimeImmutable $dateTime): bool
    {
        return $this->startAt <= $dateTime && $dateTime < $this->endAt;
    }
}
