<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="broadcast_start_at_idx", columns={"start_at"}),
 *     @ORM\Index(name="broadcast_end_at_idx", columns={"end_at"}),
 *   }
 * )
 *
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository")
 */
class Broadcast
{
    use TimestampableEntity;
    use Traits\PartnerPidTrait;

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
     * @ORM\ManyToOne(targetEntity="Version", inversedBy="broadcasts")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $programmeItem;

    /**
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $service;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $startAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $endAt;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=false)
     */
    private $duration;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isLive = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isBlanked = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isRepeat = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isCritical = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isAudioDescribed = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isWebcast = false;

    public function __construct(
        string $pid,
        Version $version,
        DateTime $start,
        DateTime $end
    ) {
        $this->pid = $pid;
        $this->version = $version;
        $this->startAt = $start;
        $this->endAt = $end;
        $this->updateDuration();
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid): void
    {
        $this->pid = $pid;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version): void
    {
        $this->version = $version;
    }

    public function getProgrammeItem(): ?ProgrammeItem
    {
        return $this->programmeItem;
    }

    public function setProgrammeItem(ProgrammeItem $programmeItem): void
    {
        $this->programmeItem = $programmeItem;
    }

    public function getService(): ?Service
    {
        return $this->service;
    }

    public function setService(?Service $service): void
    {
        $this->service = $service;
    }

    public function getIsLive(): bool
    {
        return $this->isLive;
    }

    public function setIsLive(bool $isLive): void
    {
        $this->isLive = $isLive;
    }

    public function getIsBlanked(): bool
    {
        return $this->isBlanked;
    }

    public function setIsBlanked(bool $isBlanked): void
    {
        $this->isBlanked = $isBlanked;
    }

    public function getIsRepeat(): bool
    {
        return $this->isRepeat;
    }

    public function setIsRepeat(bool $isRepeat): void
    {
        $this->isRepeat = $isRepeat;
    }

    public function getIsCritical(): bool
    {
        return $this->isCritical;
    }

    public function setIsCritical(bool $isCritical): void
    {
        $this->isCritical = $isCritical;
    }

    public function getIsAudioDescribed(): bool
    {
        return $this->isAudioDescribed;
    }

    public function setIsAudioDescribed(bool $isAudioDescribed): void
    {
        $this->isAudioDescribed = $isAudioDescribed;
    }

    public function getIsWebcast(): bool
    {
        return $this->isWebcast;
    }

    public function setIsWebcast(bool $isWebcast): void
    {
        $this->isWebcast = $isWebcast;
    }

    public function getStart(): DateTime
    {
        return $this->startAt;
    }

    public function setStart(DateTime $start): void
    {
        $this->startAt = $start;
        $this->updateDuration();
    }

    public function getEnd(): DateTime
    {
        return $this->endAt;
    }

    public function setEnd(DateTime $end): void
    {
        $this->endAt = $end;
        $this->updateDuration();
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration): void
    {
        $this->duration = $duration;
    }

    protected function updateDuration(): void
    {
        if ($this->startAt instanceof DateTime && $this->endAt instanceof DateTime) {
            $this->setDuration($this->endAt->getTimestamp() - $this->startAt->getTimestamp());
        }
    }
}
