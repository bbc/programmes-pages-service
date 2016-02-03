<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class Availability
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $status = AvailabilityStatusEnum::PENDING;

    /**
     * @var Version
     *
     * @ORM\ManyToOne(targetEntity="Version")
     */
    private $version;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $start;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $end;

    /**
     * @var ArrayCollection
     *
     * @ORM\ManyToMany(targetEntity="MediaSet")
     */
    private $mediaSets;

    public function __construct()
    {
        $this->mediaSets = new ArrayCollection();
    }

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

    /**
     * @return DateTime|null
     */
    public function getEnd()
    {
        return $this->end;
    }

    public function setEnd(DateTime $end = null)
    {
        $this->end = $end;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setStart(DateTime $start)
    {
        $this->start = $start;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        if (!in_array($status, [AvailabilityStatusEnum::AVAILABLE, AvailabilityStatusEnum::FUTURE, AvailabilityStatusEnum::PENDING])) {
            throw new InvalidArgumentException(sprintf(
                'Called setStatus with an invalid value. Expected one of "%s", "%s" or "%s" but got "%s"',
                AvailabilityStatusEnum::AVAILABLE,
                AvailabilityStatusEnum::FUTURE,
                AvailabilityStatusEnum::PENDING,
                $status
            ));
        }

        $this->status = $status;
    }

    public function getMediaSets(): ArrayCollection
    {
        return $this->mediaSets;
    }

    public function setMediaSets(ArrayCollection $mediaSets)
    {
        $this->mediaSets = $mediaSets;
    }

    public function addMediaSet(MediaSet $mediaSet)
    {
        $this->mediaSets[] = $mediaSet;
    }
}
