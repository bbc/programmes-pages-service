<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Domain\Enumeration\AvailabilityStatusEnum;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use InvalidArgumentException;

/**
 * @ORM\Entity()
 */
class RefAppwAvailability
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
     * @var Version
     *
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $version;

    /**
     * @var RefAppwProgramme
     *
     * @ORM\ManyToOne(targetEntity="RefAppwProgramme")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $appwProgramme;

    /**
     * @var RefAppwMediaSet
     *
     * @ORM\ManyToOne(targetEntity="RefAppwMediaSet")
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
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $scheduledEnd;

    /**
     * @var DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $actualStart;

    // Needed?

//    /**
//     * @var string
//     * @ORM\Column(type="string", length=32, nullable=true)
//     */
//    private $paymentType;

    public function __construct(
        Version $version,
        RefAppwProgramme $appwProgramme,
        RefAppwMediaSet $mediaSet,
        DateTime $scheduledStart
    ) {
        $this->version = $version;
        $this->appwProgramme = $appwProgramme;
        $this->mediaSet = $mediaSet;
        $this->scheduledStart = $scheduledStart;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    public function getAppwProgramme(): ProgrammeItem
    {
        return $this->appwProgramme;
    }

    public function setAppwProgramme(ProgrammeItem $appwProgramme): void
    {
        $this->appwProgramme = $appwProgramme;
    }

    public function getMediaSet(): RefAppwMediaSet
    {
        return $this->mediaSet;
    }

    public function setMediaSet(RefAppwMediaSet $mediaSet): void
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
        if (!in_array($status, AvailabilityStatusEnum::validValues())) {
            throw new InvalidArgumentException(sprintf(
                'Called setStatus with an invalid value. Expected one of %s but got "%s"',
                '"' . implode('", "', AvailabilityStatusEnum::validValues()) . '"',
                $status
            ));
        }

        $this->status = $status;
    }

//    public function getPaymentType(): ?string
//    {
//        return $this->paymentType;
//    }
//
//    public function setPaymentType(?string $paymentType): void
//    {
//        $this->paymentType = $paymentType;
//    }
}
