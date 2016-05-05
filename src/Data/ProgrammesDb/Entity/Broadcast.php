<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\DurationTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="broadcast_pid_idx", columns={"pid"}),
 *     @ORM\Index(name="broadcast_start_at_idx", columns={"start_at"}),
 *     @ORM\Index(name="broadcast_end_at_idx", columns={"end_at"}),
 *   }
 * )
 * @ORM\Entity()
 */
class Broadcast
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
     * @ORM\Column(type="string", nullable=false, unique=true)
     */
    private $pid;

    /**
     * @ORM\ManyToOne(targetEntity="Version")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $version;

    /**
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
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
    private $live = false;

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

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $critical = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $audioDescribed = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isWebcast = false;

    public function __construct(
        string $pid,
        Service $service,
        Version $version,
        DateTime $start,
        DateTime $end
    ) {
        $this->pid = $pid;
        $this->service = $service;
        $this->version = $version;
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

    public function getPid(): string
    {
        return $this->pid;
    }

    public function setPid(string $pid)
    {
        $this->pid = $pid;
    }

    public function getVersion(): Version
    {
        return $this->version;
    }

    public function setVersion(Version $version)
    {
        $this->version = $version;
    }

    public function getService(): Service
    {
        return $this->service;
    }

    public function setService(Service $service)
    {
        $this->service = $service;
    }

    public function isLive(): bool
    {
        return $this->live;
    }

    public function setLive(bool $live)
    {
        $this->live = $live;
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

    public function setRepeat(bool $isRepeat)
    {
        $this->isRepeat = $isRepeat;
    }

    public function isCritical(): bool
    {
        return $this->critical;
    }

    public function setCritical(bool $critical)
    {
        $this->critical = $critical;
    }

    public function isAudioDescribed(): bool
    {
        return $this->audioDescribed;
    }

    public function setAudioDescribed(bool $audioDescribed)
    {
        $this->audioDescribed = $audioDescribed;
    }

    public function getIsWebcast(): bool
    {
        return $this->isWebcast;
    }

    public function setIsWebcast(bool $isWebcast)
    {
        $this->isWebcast = $isWebcast;
    }

    public function getStart(): DateTime
    {
        return $this->startAt;
    }

    public function setStart(DateTime $start)
    {
        $this->startAt = $start;
        $this->updateDuration();
    }

    public function getEnd(): DateTime
    {
        return $this->endAt;
    }

    public function setEnd(DateTime $end)
    {
        $this->endAt = $end;
        $this->updateDuration();
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration)
    {
        $this->duration = $duration;
    }

    protected function updateDuration()
    {
        if ($this->startAt instanceof DateTime && $this->endAt instanceof DateTime) {
            $this->setDuration($this->endAt->getTimestamp() - $this->startAt->getTimestamp());
        }
    }
}
