<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;

class Service
{
    /** @var int */
    private $dbId;

    /** @var Sid */
    private $sid;

    /** @var string */
    private $name;

    /** @var string */
    private $shortName;

    /** @var string */
    private $urlKey;

    /** @var Network|null */
    private $network;

    /** @var DateTimeImmutable|null */
    private $startDate;

    /** @var DateTimeImmutable|null */
    private $endDate;

    /** @var string|null */
    private $liveStreamUrl;

    public function __construct(
        int $dbId,
        Sid $sid,
        Pid $pid,
        string $name,
        ?string $shortName = null,
        ?string $urlKey = null,
        ?Network $network = null,
        ?DateTimeImmutable $startDate = null,
        ?DateTimeImmutable $endDate = null,
        ?string $liveStreamUrl = null
    ) {
        $this->dbId = $dbId;
        $this->sid = $sid;
        $this->pid = $pid;
        $this->name = $name;
        $this->shortName = (!is_null($shortName) ? $shortName : $name);
        $this->urlKey = (!is_null($urlKey) ? $urlKey : (string) $sid);
        $this->network = $network;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->liveStreamUrl = $liveStreamUrl;
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

    public function getSid(): Sid
    {
        return $this->sid;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function getUrlKey(): string
    {
        return $this->urlKey;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getNetwork(): ?Network
    {
        if ($this->network instanceof UnfetchedNetwork) {
            throw new DataNotFetchedException(
                'Could not get Network of Service "'
                    . $this->sid . '" as it was not fetched'
            );
        }
        return $this->network;
    }

    /**
     * When the service started. This is inclusive, this is the very first
     * moment of the service.
     * Non-broadcast services do not have a start date.
     */
    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    /**
     * When the service ended. This is exclusive, this is the very first
     * moment that the service is no longer active.
     * On-air but not yet stopped services do not have an end date.
     *
     * Honestly PIPS is not clear on if this should be exclusive or
     * inclusive - It is pretty much a 50/50 split on services stopping at
     * :59 or :00 seconds. We go with exclusive to keep consistency with
     * Broadcast start and end times.
     */
    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getLiveStreamUrl(): ?string
    {
        return $this->liveStreamUrl;
    }

    /**
     * Returns true if the service is active on a given date.
     * If a service has no start date then we assume it was active since the
     * dawn of time.
     * If a service has no end date then we assume it will be active until the
     * end of time.
     * Service starts are inclusive and ends are exclusive.
     */
    public function isActiveAt(DateTimeImmutable $dateTime): bool
    {
        return (!$this->startDate || $this->startDate <= $dateTime) && (!$this->endDate || $dateTime < $this->endDate);
    }

    /**
     * Returns true if this service is part of a TV network
     */
    public function isTv(): bool
    {
        // Network can be null, but unfetched should throw an exception
        if ($this->network && $this->getNetwork()->getMedium() === NetworkMediumEnum::TV) {
            return true;
        }
        return false;
    }

    /**
     * Returns true if this service is part of a radio network
     */
    public function isRadio(): bool
    {
        // Network can be null, but unfetched should throw an exception
        if ($this->network && $this->getNetwork()->getMedium() === NetworkMediumEnum::RADIO) {
            return true;
        }
        return false;
    }
}
