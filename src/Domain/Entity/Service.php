<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
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

    public function getSid(): string
    {
        return $this->sid;
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

    public function getStartDate(): ?DateTimeImmutable
    {
        return $this->startDate;
    }

    public function getEndDate(): ?DateTimeImmutable
    {
        return $this->endDate;
    }

    public function getLiveStreamUrl(): ?string
    {
        return $this->liveStreamUrl;
    }
}
