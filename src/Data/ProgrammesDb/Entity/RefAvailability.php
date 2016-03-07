<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use Doctrine\ORM\Mapping as ORM;
use DateTime;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class RefAvailability
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
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    /**
     * @var Version
     *
     * @ORM\ManyToOne(targetEntity="Version")
     */
    private $version;

    /**
     * @var ProgrammeItem
     *
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     */
    private $programmeItem;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $scheduledStart;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $scheduledEnd;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actualStart;

    /**
     * @var RefMediaSet
     *
     * @ORM\ManyToOne(targetEntity="RefMediaSet")
     */
    private $mediaSet;


    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * @return Version|null
     */
    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion(Version $version = null)
    {
        $this->version = $version;
    }

    /**
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
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
        $this->programmeItem = $programmeItem;
    }

    /**
     * @return DateTime
     */
    public function getScheduledStart()
    {
        return $this->scheduledStart;
    }

    public function setScheduledStart(DateTime $scheduledStart)
    {
        $this->scheduledStart = $scheduledStart;
    }

    /**
     * @return DateTime
     */
    public function getScheduledEnd()
    {
        return $this->scheduledEnd;
    }

    /**
     * @param DateTime $scheduledEnd
     */
    public function setScheduledEnd($scheduledEnd)
    {
        $this->scheduledEnd = $scheduledEnd;
    }

    /**
     * @return DateTime
     */
    public function getActualStart()
    {
        return $this->actualStart;
    }

    public function setActualStart(DateTime $actualStart)
    {
        $this->actualStart = $actualStart;
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

    public function getMediaSet(): RefMediaSet
    {
        return $this->mediaSet;
    }

    public function setMediaSet(RefMediaSet $mediaSet)
    {
        $this->mediaSet = $mediaSet;
    }
}
