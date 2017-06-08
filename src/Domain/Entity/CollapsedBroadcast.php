<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use DateTimeImmutable;
use InvalidArgumentException;

class CollapsedBroadcast
{
    /** @var ProgrammeItem */
    private $programmeItem;

    /** @var Service[] */
    private $services;

    /** @var string */
    private $startAt;

    /** @var string */
    private $endAt;

    /** @var int */
    private $duration;

    /** @var bool */
    private $isBlanked;

    /** @var bool */
    private $isRepeat;

    public function __construct(
        ProgrammeItem $programmeItem,
        array $services,
        DateTimeImmutable $startAt,
        DateTimeImmutable $endAt,
        int $duration,
        bool $isBlanked = false,
        bool $isRepeat = false
    ) {
        $this->assertArrayOfServices($services);

        $this->programmeItem = $programmeItem;
        $this->services = $services;
        $this->startAt = $startAt;
        $this->endAt = $endAt;
        $this->duration = $duration;
        $this->isBlanked = $isBlanked;
        $this->isRepeat = $isRepeat;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getProgrammeItem(): ProgrammeItem
    {
        if ($this->programmeItem instanceof UnfetchedProgrammeItem) {
            throw new DataNotFetchedException('Could not get ProgrammeItem of CollapsedBroadcast as it was not fetched');
        }

        return $this->programmeItem;
    }

    /**
     * @return Service[]
     */
    public function getServices(): array
    {
        return $this->services;
    }

    public function getStartAt(): DateTimeImmutable
    {
        return $this->startAt;
    }

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

    /**
     * @throws InvalidArgumentException
     */
    private function assertArrayOfServices(array $array): void
    {
        if (empty($array)) {
            throw new InvalidArgumentException(
                'Tried to create a CollapsedBroadcast with invalid Services. Expected a non-empty array of Services but the array was empty'
            );
        }

        foreach ($array as $item) {
            if (!$item instanceof Service) {
                throw new InvalidArgumentException(sprintf(
                    'Tried to create a CollapsedBroadcast with invalid Services. Expected a non-empty array of Services but the array contained an instance of "%s"',
                    (is_object($item) ? get_class($item) : gettype($item))
                ));
            }
        }
    }
}
