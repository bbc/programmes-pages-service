<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;

class Service
{
    /**
     * @var Sid
     */
    private $sid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var string
     */
    private $shortName;

    /**
     * @var string
     */
    private $urlKey;

    /**
     * @var Network|null
     */
    private $network;

    /**
     * @var DateTimeImmutable|null
     */
    private $startDate;

    /**
     * @var DateTimeImmutable|null
     */
    private $endDate;

    /**
     * @var string|null
     */
    private $liveStreamUrl;

    public function __construct(
        Sid $sid,
        string $name,
        string $shortName = null,
        string $urlKey = null,
        Network $network = null,
        DateTimeImmutable $startDate = null,
        DateTimeImmutable $endDate = null,
        string $liveStreamUrl = null
    ) {
        $this->sid = $sid;
        $this->name = $name;
        $this->shortName = (!is_null($shortName) ? $shortName : $name);
        $this->urlKey = (!is_null($urlKey) ? $urlKey : (string) $sid);
        $this->network = $network;
        $this->startDate = $startDate;
        $this->endDate = $endDate;
        $this->liveStreamUrl = $liveStreamUrl;
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
     * @return Network|null
     */
    public function getNetwork()
    {
        return $this->network;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    /**
     * @return DateTimeImmutable|null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    /**
     * @return string|null
     */
    public function getLiveStreamUrl()
    {
        return $this->liveStreamUrl;
    }
}
