<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="pid_idx", columns={"pid"}),
 *     @ORM\Index(name="vpid_idx", columns={"broadcast_of"}),
 *     @ORM\Index(name="service_idx", columns={"broadcaster"})
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
    private $broadcastOf;

    /**
     * @ORM\ManyToOne(targetEntity="Service")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $broadcaster;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $start;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $end;

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
    private $repeat = false;

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

    public function __construct(
        string $pid,
        Service $broadcaster,
        Version $broadcastOf,
        DateTime $start,
        DateTime $end,
        int $duration
    ) {
        $this->pid = $pid;
        $this->broadcastOf = $broadcastOf;
        $this->broadcaster = $broadcaster;
        $this->start = $start;
        $this->end = $end;
        $this->duration = $duration;
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

    public function getBroadcastOf(): Version
    {
        return $this->broadcastOf;
    }

    public function setBroadcastOf(Version $broadcastOf)
    {
        $this->broadcastOf = $broadcastOf;
    }

    public function getBroadcaster(): Service
    {
        return $this->broadcaster;
    }

    public function setBroadcaster(Service $broadcaster)
    {
        $this->broadcaster = $broadcaster;
    }

    public function getStart(): DateTime
    {
        return $this->start;
    }

    public function setStart(DateTime $start)
    {
        $this->start = $start;
    }

    public function getEnd(): DateTime
    {
        return $this->end;
    }

    public function setEnd(DateTime $end)
    {
        $this->end = $end;
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration)
    {
        $this->duration = $duration;
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
        return $this->repeat;
    }

    public function setRepeat(bool $repeat)
    {
        $this->repeat = $repeat;
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
}
