<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
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
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $status = AvailabilityStatusEnum::PENDING;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
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

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    public function getProgrammeItem(): ProgrammeItem
    {
        return $this->programmeItem;
    }

    public function setProgrammeItem(ProgrammeItem $programmeItem): void
    {
        $this->programmeItem = $programmeItem;
    }

    public function getMediaSet(): RefMediaSet
    {
        return $this->mediaSet;
    }

    public function setMediaSet(RefMediaSet $mediaSet): void
    {
        $this->mediaSet = $mediaSet;
    }

    public function getScheduledStart(): DateTime
    {
        return $this->scheduledStart;
    }

    public function setScheduledStart(DateTime $scheduledStart): void
    {
        $this->scheduledStart = $scheduledStart;
    }

    public function getScheduledEnd(): ?DateTime
    {
        return $this->scheduledEnd;
    }

    public function setScheduledEnd(?DateTime $scheduledEnd): void
    {
        $this->scheduledEnd = $scheduledEnd;
    }

    public function getActualStart(): ?DateTime
    {
        return $this->actualStart;
    }

    public function setActualStart(?DateTime $actualStart): void
    {
        $this->actualStart = $actualStart;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
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
