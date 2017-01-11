<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DateTime;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="service_url_key_idx", columns={"url_key"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository")
 */
class Service
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;

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
    private $sid;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $urlKey;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $mediaType;

    /**
     * @var MasterBrand|null
     *
     * @ORM\ManyToOne(targetEntity="MasterBrand")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $masterBrand;

    /**
     * @var Network|null
     *
     * @ORM\ManyToOne(targetEntity="Network", cascade={"persist"})
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $network;

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

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $liveStreamUrl;

    public function __construct(string $sid, string $pid, string $name, string $type, string $mediaType)
    {
        $this->sid = $sid;
        $this->pid = $pid;
        $this->name = $name;
        $this->type = $type;
        $this->mediaType = $mediaType;

        // Default values for these two are based on other inputs
        $this->shortName = $name;
        $this->urlKey = $sid;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSid(): string
    {
        return $this->sid;
    }

    public function setSid(string $sid): void
    {
        $this->sid = $sid;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid): void
    {
        // TODO Validate PID

        $this->pid = $pid;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name): void
    {
        $this->name = $name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName): void
    {
        $this->shortName = $shortName;
    }

    public function getUrlKey(): string
    {
        return $this->urlKey;
    }

    public function setUrlKey(string $urlKey): void
    {
        $this->urlKey = $urlKey;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType): void
    {
        $this->mediaType = $mediaType;
    }

    public function getMasterBrand(): ?MasterBrand
    {
        return $this->masterBrand;
    }

    public function setMasterBrand(?MasterBrand $masterBrand): void
    {
        $this->masterBrand = $masterBrand;
    }

    public function getNetwork(): ?Network
    {
        return $this->network;
    }

    public function setNetwork(?Network $network): void
    {
        $this->network = $network;
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

    public function getLiveStreamUrl(): ?string
    {
        return $this->liveStreamUrl;
    }

    public function setLiveStreamUrl(?string $liveStreamUrl): void
    {
        $this->liveStreamUrl = $liveStreamUrl;
    }
}
