<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsEmbargoedTrait;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity()
 */
class Version
{
    use IsEmbargoedTrait;
    use TimestampableEntity;

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
     * @var string|null
     * @ORM\Column(type="string", nullable=true)
     */
    private $guidanceWarningCodes;

    /**
     * @var string
     * @ORM\Column(type="boolean")
     */
    private $competitionWarning = false;

    /**
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     * @ORM\JoinColumn(onDelete="SET NULL")
     */
    private $programmeItem;

    /**
     * @ORM\ManyToMany(targetEntity="VersionType", cascade="persist")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $versionTypes;

    public function __construct(string $pid)
    {
        $this->pid = $pid;
        $this->versionTypes = new ArrayCollection();
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid)
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

    public function getCompetitionWarning(): bool
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

    public function getVersionTypes(): DoctrineCollection
    {
        return $this->versionTypes;
    }

    public function setVersionTypes(DoctrineCollection $versionTypes)
    {
        $this->versionTypes = $versionTypes;
    }
}
