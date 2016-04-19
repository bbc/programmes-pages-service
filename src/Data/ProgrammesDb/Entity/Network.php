<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *   indexes={ @ORM\Index(name="url_key_idx", columns={"urlKey"})}
 * )
 */
class Network
{
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
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $nid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $urlKey;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $type;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $medium;

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

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getNid(): string
    {
        return $this->nid;
    }

    public function setNid(string $nid)
    {
        $this->nid = $nid;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    /**
     * @return string|null
     */
    public function getUrlKey()
    {
        return $this->urlKey;
    }

    public function setUrlKey(string $urlKey = null)
    {
        $this->urlKey = $urlKey;
    }

    /**
     * @return string|null
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type = null)
    {
        $this->type = $type;
    }

    /**
     * @return string|null
     */
    public function getMedium()
    {
        return $this->medium;
    }

    public function setMedium(string $medium = null)
    {
        $this->medium = $medium;
    }

    /**
     * @return Service|null
     */
    public function getDefaultService()
    {
        return $this->defaultService;
    }

    public function setDefaultService(Service $defaultService = null)
    {
        $this->defaultService = $defaultService;
    }


    public function getIsPublicOutlet(): bool
    {
        return $this->isPublicOutlet;
    }

    public function setIsPublicOutlet(bool $isPublicOutlet)
    {
        $this->isPublicOutlet = $isPublicOutlet;
    }

    public function getIsChildrens(): bool
    {
        return $this->isChildrens;
    }

    public function setIsChildrens(bool $isChildrens)
    {
        $this->isChildrens = $isChildrens;
    }

    public function getIsWorldServiceInternational(): bool
    {
        return $this->isWorldServiceInternational;
    }

    public function setIsWorldServiceInternational(bool $isWorldServiceInternational)
    {
        $this->isWorldServiceInternational = $isWorldServiceInternational;
    }

    public function getIsInternational(): bool
    {
        return $this->isInternational;
    }

    public function setIsInternational(bool $isInternational)
    {
        $this->isInternational = $isInternational;
    }

    public function getIsAllowedAdverts(): bool
    {
        return $this->isAllowedAdverts;
    }

    public function setIsAllowedAdverts(bool $isAllowedAdverts)
    {
        $this->isAllowedAdverts = $isAllowedAdverts;
    }

    /**
     * @return DateTime|null
     */
    public function getStartDate()
    {
        return $this->startDate;
    }

    public function setStartDate(DateTime $startDate = null)
    {
        $this->startDate = $startDate;
    }

    /**
     * @return DateTime|null
     */
    public function getEndDate()
    {
        return $this->endDate;
    }

    public function setEndDate(DateTime $endDate = null)
    {
        $this->endDate = $endDate;
    }
}