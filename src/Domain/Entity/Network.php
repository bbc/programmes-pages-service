<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use InvalidArgumentException;

class Network
{
    /**
     * @var Nid
     */
    private $nid;

    /**
     * @var string
     */
    private $name;

    /**
     * @var Image
     */
    private $image;

    /**
     * @var string|null
     */
    private $urlKey;

    /**
     * @var string|null
     */
    private $type;

    /**
     * @var string|null
     */
    private $medium;

    /**
     * @var Service|null
     */
    private $defaultService;

    /**
     * @var bool
     */
    private $isPublicOutlet;

    /**
     * @var bool
     */
    private $isChildrens;

    /**
     * @var bool
     */
    private $isWorldServiceInternational;

    /**
     * @var bool
     */
    private $isInternational;

    /**
     * @var bool
     */
    private $isAllowedAdverts;

    public function __construct(
        Nid $nid,
        string $name,
        Image $image,
        string $urlKey = null,
        string $type = null,
        string $medium = null,
        Service $defaultService = null,
        bool $isPublicOutlet = false,
        bool $isChildrens = false,
        bool $isWorldServiceInternational = false,
        bool $isInternational = false,
        bool $isAllowedAdverts = false
    ) {
        if (!in_array($medium, [NetworkMediumEnum::RADIO, NetworkMediumEnum::TV, NetworkMediumEnum::UNKNOWN])) {
            throw new InvalidArgumentException(sprintf(
                '$medium has an invalid value. Expected one of "%s", "%s" or "%s" but got "%s"',
                NetworkMediumEnum::RADIO,
                NetworkMediumEnum::TV,
                NetworkMediumEnum::UNKNOWN,
                $medium
            ));
        }

        $this->nid = $nid;
        $this->name = $name;
        $this->image = $image;
        $this->urlKey = $urlKey;
        $this->type = $type;
        $this->medium = $medium;
        $this->defaultService = $defaultService;
        $this->isPublicOutlet = $isPublicOutlet;
        $this->isChildrens = $isChildrens;
        $this->isWorldServiceInternational = $isWorldServiceInternational;
        $this->isInternational = $isInternational;
        $this->isAllowedAdverts = $isAllowedAdverts;
    }

    public function getNid(): Nid
    {
        return $this->nid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    /**
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->urlKey;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    public function getMedium(): string
    {
        return $this->medium;
    }

    /**
     * @return Service|null
     */
    public function getDefaultService()
    {
        return $this->defaultService;
    }


    public function isPublicOutlet(): bool
    {
        return $this->isPublicOutlet;
    }

    public function isChildrens(): bool
    {
        return $this->isChildrens;
    }

    public function isWorldServiceInternational(): bool
    {
        return $this->isWorldServiceInternational;
    }

    public function isInternational(): bool
    {
        return $this->isInternational;
    }

    public function isAllowedAdverts(): bool
    {
        return $this->isAllowedAdverts;
    }
}
