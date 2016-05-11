<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="version_pid_idx", columns={"pid"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\VersionRepository")
 */
class Version
{
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
     * @ORM\Column(type="string", length=15, nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @var string|null
     * @ORM\Column(type="string", length=255, nullable=true)
     */
    private $guidanceWarningCodes;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $competitionWarning = false;

    /**
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $programmeItem;

    /**
     * @ORM\ManyToMany(targetEntity="VersionType", cascade="persist")
     * @ORM\JoinColumn(onDelete="CASCADE")
     */
    private $versionTypes;

    public function __construct(string $pid, ProgrammeItem $programmeItem)
    {
        $this->pid = $pid;
        $this->versionTypes = new ArrayCollection();
        $this->setProgrammeItem($programmeItem);
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
