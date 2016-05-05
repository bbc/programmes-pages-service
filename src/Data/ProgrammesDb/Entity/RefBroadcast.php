<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\DurationTrait;
use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="pid_idx", columns={"pid"}),
 *     @ORM\Index(name="vpid_idx", columns={"broadcast_of_id"}),
 *     @ORM\Index(name="service_idx", columns={"broadcaster_id"})
 *   }
 * )
 * @ORM\Entity()
 */
class RefBroadcast
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
        Service $broadcaster,
        Version $broadcastOf,
        DateTime $start,
        DateTime $end
    ) {
        $this->pid = $pid;
        $this->broadcaster = $broadcaster;
        $this->broadcastOf = $broadcastOf;
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
}
