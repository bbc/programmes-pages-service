<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\PersistentCollection;

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
     *
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $duration;

    /**
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     */
    private $programmeItem;

    /**
     * @ORM\ManyToMany(targetEntity="VersionType",cascade="persist")
     */
    private $versionTypes;

    public function __construct()
    {
        $this->versionTypes = new ArrayCollection();
    }

    public function getId(): int
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

    public function setDuration(int $duration)
    {
        $this->duration = $duration;
    }

    public function setProgrammeItem(ProgrammeItem $programme)
    {
        return $this->programme = $programme;
    }

    /**
     * @return ProgrammeItem
     */
    public function getProgrammeItem()
    {
        return $this->programmeItem;
    }

    public function getVersionTypes(): PersistentCollection
    {
        return $this->versionTypes;
    }

    public function setVersionTypes(ArrayCollection $versionTypes)
    {
        $this->versionTypes = $versionTypes;
    }

    public function addVersionType(VersionType $versionType)
    {
        $this->versionTypes[] = $versionType;
    }
}
