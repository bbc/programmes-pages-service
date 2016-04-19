<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use DateTime;

/**
 * @ORM\Entity()
 * @ORM\Table(
 *   indexes={ @ORM\Index(name="mid_idx", columns={"mid"})}
 * )
 */
class MasterBrand
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
    private $mid;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $name;

    /**
     * @var Network|null
     *
     * @ORM\ManyToOne(targetEntity="Network")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $network;

    /**
     * @var Version|null
     *
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $competitionWarning;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $colour;

    /**
     * @var string|null
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $urlKey;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

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

    public function __construct(string $mid, string $name)
    {
        $this->mid = $mid;
        $this->name = $name;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getMid(): string
    {
        return $this->mid;
    }

    public function setMid(string $mid)
    {
        $this->mid = $mid;
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
     * @return Version|null
     */
    public function getCompetitionWarning()
    {
        return $this->competitionWarning;
    }

    public function setCompetitionWarning(Version $competitionWarning = null)
    {
        $this->competitionWarning = $competitionWarning;
    }

    /**
     * @return string|null
     */
    public function getColour()
    {
        return $this->colour;
    }

    public function setColour(string $colour = null)
    {
        $this->colour = $colour;
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
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition(int $position = null)
    {
        $this->position = $position;
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
