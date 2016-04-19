<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use DateTime;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class RefAvailability
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
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $version;

    /**
     * @var ProgrammeItem
     *
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $programmeItem;

    /**
     * @var RefMediaSet
     *
     * @ORM\ManyToOne(targetEntity="RefMediaSet")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $mediaSet;

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

    public function __construct(
        string $type,
        Version $version,
        ProgrammeItem $programmeItem,
        RefMediaSet $mediaSet,
        DateTime $scheduledStart
    ) {
        $this->type = $type;
        $this->version = $version;
        $this->programmeItem = $programmeItem;
        $this->mediaSet = $mediaSet;
        $this->scheduledStart = $scheduledStart;
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version)
    {
        $this->version = $version;
    }

    public function getProgrammeItem(): ProgrammeItem
    {
        return $this->programmeItem;
    }

    public function setProgrammeItem(ProgrammeItem $programmeItem)
    {
        $this->programmeItem = $programmeItem;
    }

    public function getMediaSet(): RefMediaSet
    {
        return $this->mediaSet;
    }

    public function setMediaSet(RefMediaSet $mediaSet)
    {
        $this->mediaSet = $mediaSet;
    }

    public function getScheduledStart(): DateTime
    {
        return $this->scheduledStart;
    }

    public function setScheduledStart(DateTime $scheduledStart)
    {
        $this->scheduledStart = $scheduledStart;
    }

    /**
     * @return DateTime|null
     */
    public function getScheduledEnd()
    {
        return $this->scheduledEnd;
    }

    public function setScheduledEnd(DateTime $scheduledEnd = null)
    {
        $this->scheduledEnd = $scheduledEnd;
    }

    /**
     * @return DateTime|null
     */
    public function getActualStart()
    {
        return $this->actualStart;
    }

    public function setActualStart(DateTime $actualStart = null)
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
}
