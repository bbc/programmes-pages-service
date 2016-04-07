<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Version
{

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string
     * @ORM\Column(type="string", nullable=true)
     */
    private $guidanceWarningCodes;

    /**
     * @var string
     * @ORM\Column(type="boolean", options={"default":false})
     */
    private $competitionWarning = false;

    /**
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $programmeItem;

    /**
     * @ORM\ManyToMany(targetEntity="VersionType",cascade="persist")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $versionTypes;

    public function __construct()
    {
        $this->versionTypes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return string|null
     */
    public function getPid()
    {
        return $this->pid;
    }

    public function setPid(string $pid = null)
    {
        $this->pid = $pid;
    }

    /**
     * @return int|null
     */
    public function getDuration()
    {
        return $this->duration;
    }

    public function setDuration(int $duration = null)
    {
        $this->duration = $duration;
    }

    /**
     * @return string|null
     */
    public function getGuidanceWarningCodes()
    {
        return $this->guidanceWarningCodes;
    }

    public function setGuidanceWarningCodes(string $guidanceWarningCodes = null)
    {
        $this->guidanceWarningCodes = $guidanceWarningCodes;
    }

    /**
     * @return bool
     */
    public function getCompetitionWarning()
    {
        return $this->competitionWarning;
    }

    public function setCompetitionWarning(bool $competitionWarning)
    {
        $this->competitionWarning = $competitionWarning;
    }

    /**
     * @return ProgrammeItem
     */
    public function getProgrammeItem()
    {
        return $this->programmeItem;
    }

    public function setProgrammeItem(ProgrammeItem $programmeItem)
    {
        return $this->programmeItem = $programmeItem;
    }

    public function getVersionTypes(): Collection
    {
        return $this->versionTypes;
    }

    public function setVersionTypes(Collection $versionTypes)
    {
        $this->versionTypes = $versionTypes;
    }
}
