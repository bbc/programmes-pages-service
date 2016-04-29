<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\DurationTrait;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * "An Simulcast is a combination of all the simultaneous broadcasts of a version."
 *
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="simulcast_start_at_idx", columns={"start_at"})
 *     @ORM\Index(name="simulcast_end_at_idx", columns={"end_at"})
 *   }
 * )
 * @ORM\Entity()
 */
class Simulcast
{
    use DurationTrait;
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
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $version;

    /**
     * @ORM\ManyToMany(targetEntity="Service")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $services;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $blanked = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isRepeat = false;

    public function __construct(Version $version, DateTime $start, DateTime $end)
    {
        $this->version = $version;
        $this->services = new ArrayCollection();
        $this->startAt = $start;
        $this->endAt = $end;
        $this->updateDuration();
    }

    /**
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version)
    {
        $this->version = $version;
    }

    public function getServices(): ArrayCollection
    {
        return $this->services;
    }

    public function addService(Service $service)
    {
        $this->services[] = $service;
    }

    public function setServices(ArrayCollection $services)
    {
        $this->services = $services;
    }

    public function isBlanked(): bool
    {
        return $this->blanked;
    }

    public function setBlanked(bool $blanked)
    {
        $this->blanked = $blanked;
    }

    public function isRepeat(): bool
    {
        return $this->isRepeat;
    }

    public function setRepeat(bool $repeat)
    {
        $this->isRepeat = $repeat;
    }
}
