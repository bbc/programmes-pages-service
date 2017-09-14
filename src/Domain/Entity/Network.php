<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedImage;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;
use InvalidArgumentException;

class Network
{
    /** @var Nid */
    private $nid;

    /** @var string */
    private $name;

    /** @var Image */
    private $image;

    /** @var string|null */
    private $urlKey;

    /** @var string|null */
    private $type;

    /** @var string|null */
    private $medium;

    /** @var Service|null */
    private $defaultService;

    /** @var bool */
    private $isPublicOutlet;

    /** @var bool */
    private $isChildrens;

    /** @var bool */
    private $isWorldServiceInternational;

    /** @var bool */
    private $isInternational;

    /** @var bool */
    private $isAllowedAdverts;

    /** @var Options */
    private $options;

    /** @var Service[]|null */
    private $services;

    public function __construct(
        Nid $nid,
        string $name,
        Image $image,
        Options $options,
        ?string $urlKey = null,
        ?string $type = null,
        ?string $medium = NetworkMediumEnum::UNKNOWN,
        ?Service $defaultService = null,
        bool $isPublicOutlet = false,
        bool $isChildrens = false,
        bool $isWorldServiceInternational = false,
        bool $isInternational = false,
        bool $isAllowedAdverts = false,
        ?array $services = null
    ) {
        if (!in_array($medium, NetworkMediumEnum::validValues(), true)) {
            throw new InvalidArgumentException(sprintf(
                '$medium has an invalid value. Expected one of %s but got "%s"',
                '"' . implode('", "', NetworkMediumEnum::validValues()) . '"',
                $medium
            ));
        }

        $this->nid = $nid;
        $this->name = $name;
        $this->image = $image;
        $this->urlKey = $urlKey;
        $this->type = $type;
        $this->medium = $medium;
        $this->options = $options;
        $this->defaultService = $defaultService;
        $this->isPublicOutlet = $isPublicOutlet;
        $this->isChildrens = $isChildrens;
        $this->isWorldServiceInternational = $isWorldServiceInternational;
        $this->isInternational = $isInternational;
        $this->isAllowedAdverts = $isAllowedAdverts;
        $this->services = $services;
    }

    public function getNid(): Nid
    {
        return $this->nid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getImage(): Image
    {
        if ($this->image instanceof UnfetchedImage) {
            throw new DataNotFetchedException(
                'Could not get Image of Network "'
                . $this->nid . '" as it was not fetched'
            );
        }
        return $this->image;
    }

    public function getUrlKey(): ?string
    {
        return $this->urlKey;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function getMedium(): ?string
    {
        return $this->medium;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getDefaultService(): ?Service
    {
        if ($this->defaultService instanceof UnfetchedService) {
            throw new DataNotFetchedException(
                'Could not get Default Service of Network "'
                    . $this->nid . '" as it was not fetched'
            );
        }

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

    public function isTv(): bool
    {
        return ($this->getMedium() === NetworkMediumEnum::TV);
    }

    public function isRadio(): bool
    {
        return ($this->getMedium() === NetworkMediumEnum::RADIO);
    }

    public function getOptions(): Options
    {
        return $this->options;
    }

    public function getOption(string $key)
    {
        return $this->options->getOption($key);
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getServices(): array
    {
        if (is_null($this->services)) {
            throw new DataNotFetchedException(
                'Could not get Services of Network "' . $this->nid . '" as it was not fetched'
            );
        }

        return $this->services;
    }
}
