<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DateTime;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="service_sid_idx", columns={"sid"}),
 *     @ORM\Index(name="service_url_key_idx", columns={"url_key"}),
 * })
 * @ORM\Entity()
 */
class Service
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
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $sid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $shortName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $urlKey;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $mediaType;

    /**
     * @var MasterBrand|null
     *
     * @ORM\ManyToOne(targetEntity="MasterBrand")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $masterBrand;

    /**
     * @var Network|null
     *
     * @ORM\ManyToOne(targetEntity="Network")
     * @ORM\JoinColumn(onDelete="SET NULL")
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
     * @ORM\Column(type="string", nullable=true)
     */
    private $liveStreamUrl;

    public function __construct(string $sid, string $name, string $type, string $mediaType)
    {
        $this->sid = $sid;
        $this->name = $name;
        $this->type = $type;
        $this->mediaType = $mediaType;

        // Default values for these two are based on other inputs
        $this->shortName = $name;
        $this->urlKey = $sid;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getSid(): string
    {
        return $this->sid;
    }

    public function setSid(string $sid)
    {
        $this->sid = $sid;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getName(): string
    {
        return $this->name;
    }

    public function setName(string $name)
    {
        $this->name = $name;
    }

    public function getShortName(): string
    {
        return $this->shortName;
    }

    public function setShortName(string $shortName)
    {
        $this->shortName = $shortName;
    }

    public function getUrlKey(): string
    {
        return $this->urlKey;
    }

    public function setUrlKey(string $urlKey)
    {
        $this->urlKey = $urlKey;
    }

    public function getMediaType(): string
    {
        return $this->mediaType;
    }

    public function setMediaType(string $mediaType)
    {
        $this->mediaType = $mediaType;
    }

    /**
     * @return MasterBrand|null
     */
    public function getMasterBrand()
    {
        return $this->masterBrand;
    }

    public function setMasterBrand(MasterBrand $masterBrand = null)
    {
        $this->masterBrand = $masterBrand;
    }

    /**
     * @return Network|null
     */
    public function getNetwork()
    {
        return $this->network;
    }

    public function setNetwork(Network $network = null)
    {
        $this->network = $network;
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

    /**
     * @return string|null
     */
    public function getLiveStreamUrl()
    {
        return $this->liveStreamUrl;
    }

    public function setLiveStreamUrl(string $liveStreamUrl = null)
    {
        $this->liveStreamUrl = $liveStreamUrl;
    }
}
