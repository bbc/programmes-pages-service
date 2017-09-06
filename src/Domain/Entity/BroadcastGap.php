<?php
declare(strict_types = 1);
namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use DateTimeImmutable;

class BroadcastGap implements BroadcastInfoInterface
{
    /** @var Service */
    private $service;

    /** @var DateTimeImmutable */
    private $startAt;

    /** @var DateTimeImmutable */
    private $endAt;

    public function __construct(
        Service $service,
        DateTimeImmutable $startAt,
        DateTimeImmutable $endAt
    ) {
        $this->service = $service;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getService(): Service
    {
        if ($this->service instanceof UnfetchedService) {
            throw new DataNotFetchedException('Could not get Service of BroadcastGap as it was not fetched');
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

    public function isOnAir(): bool
    {
        return $this->isOnAirAt(ApplicationTime::getTime());
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
