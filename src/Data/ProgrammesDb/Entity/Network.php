<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use InvalidArgumentException;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="network_url_key_idx", columns={"url_key"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\NetworkRepository")
 */
class Network
{
    use TimestampableEntity;

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false, unique=true)
     */
    private $nid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $urlKey;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=32, nullable=true)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $medium = NetworkMediumEnum::UNKNOWN;

    /**
     * @var Image|null
     *
     * @ORM\ManyToOne(targetEntity="Image")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $image;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @var Service|null
     *
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $defaultService;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isPublicOutlet = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isChildrens = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isWorldServiceInternational = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isInternational = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isAllowedAdverts = false;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $startDate;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $endDate;

    public function __construct(string $nid, string $name)
    {
        $this->nid = $nid;
        $this->name = $name;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNid(): string
    {
        return $this->nid;
    }

    public function setNid(string $nid): void
    {
        $this->nid = $nid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getUrlKey(): ?string
    {
        return $this->urlKey;
    }

    public function setUrlKey(?string $urlKey): void
    {
        $this->urlKey = $urlKey;
    }

    public function getType(): ?string
    {
        return $this->type;
    }

    public function setType(?string $type)
    {
        $this->type = $type;
    }

    public function getMedium(): string
    {
        return $this->medium;
    }

    public function setMedium(string $medium): void
    {
        if (!in_array($medium, NetworkMediumEnum::validValues())) {
            throw new InvalidArgumentException(sprintf(
                'Called setMedium with an invalid value. Expected one of %s but got "%s"',
                '"' . implode('", "', NetworkMediumEnum::validValues()) . '"',
                $medium
            ));
        }

        $this->medium = $medium;
    }

    public function getImage(): ?Image
    {
        return $this->image;
    }

    public function setImage(?Image $image): void
    {
        $this->image = $image;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    public function getDefaultService(): ?Service
    {
        return $this->defaultService;
    }

    public function setDefaultService(?Service $defaultService)
    {
        $this->defaultService = $defaultService;
    }


    public function getIsPublicOutlet(): bool
    {
        return $this->isPublicOutlet;
    }

    public function setIsPublicOutlet(bool $isPublicOutlet): void
    {
        $this->isPublicOutlet = $isPublicOutlet;
    }

    public function getIsChildrens(): bool
    {
        return $this->isChildrens;
    }

    public function setIsChildrens(bool $isChildrens): void
    {
        $this->isChildrens = $isChildrens;
    }

    public function getIsWorldServiceInternational(): bool
    {
        return $this->isWorldServiceInternational;
    }

    public function setIsWorldServiceInternational(bool $isWorldServiceInternational): void
    {
        $this->isWorldServiceInternational = $isWorldServiceInternational;
    }

    public function getIsInternational(): bool
    {
        return $this->isInternational;
    }

    public function setIsInternational(bool $isInternational): void
    {
        $this->isInternational = $isInternational;
    }

    public function getIsAllowedAdverts(): bool
    {
        return $this->isAllowedAdverts;
    }

    public function setIsAllowedAdverts(bool $isAllowedAdverts): void
    {
        $this->isAllowedAdverts = $isAllowedAdverts;
    }

    public function getStartDate(): ?DateTime
    {
        return $this->startDate;
    }

    public function setStartDate(?DateTime $startDate): void
    {
        $this->startDate = $startDate;
    }

    public function getEndDate(): ?DateTime
    {
        return $this->endDate;
    }

    public function setEndDate(?DateTime $endDate): void
    {
        $this->endDate = $endDate;
    }
}
