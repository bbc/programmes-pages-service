<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Table(
 *   indexes={
 *     @ORM\Index(name="collapsed_broadcast_start_at_idx", columns={"start_at"}),
 *     @ORM\Index(name="collapsed_broadcast_end_at_idx", columns={"end_at"}),
 *     @ORM\Index(name="collapsed_broadcast_tleo_end_at_idx", columns={"tleo_id","end_at"}),
 *     @ORM\Index(name="collapsed_broadcast_tleo_start_at_idx", columns={"tleo_id","start_at"}),
 *   }
 * )
 *
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository")
 */
class CollapsedBroadcast
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
     * @ORM\Column(type="text", nullable=false)
     */
    private $broadcastIds;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $serviceIds;

    /**
     * @var string
     * @ORM\Column(type="text", nullable=false)
     */
    private $areWebcasts;

    /**
     * @ORM\ManyToOne(targetEntity="ProgrammeItem")
     * @ORM\JoinColumn(nullable=true, onDelete="CASCADE")
     */
    private $programmeItem;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $startAt;

    /**
     * @var DateTime
     *
     * This is not guaranteed to be accurate due to group by.
     * We choose the earliest one.
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $endAt;

    /**
     * @var int|null
     *
     * This is not guaranteed to be accurate due to group by.
     * We choose the shortest one.
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $duration;

    /**
     * @var bool
     *
     * This is not guaranteed to be accurate due to group by.
     * This value is true if any of the values for the group are true.
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isBlanked = false;

    /**
     * @var bool
     *
     * This is not guaranteed to be accurate due to group by.
     * This value is false if any of the values for the group are false.
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isRepeat = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isWebcastOnly = false;

    /**
     * @var Programme
     *
     * @ORM\ManyToOne(targetEntity="Programme")
     * @ORM\JoinColumn(nullable=true, onDelete="SET NULL")
     */
    private $tleo;

    public function __construct(
        ProgrammeItem $programmeItem,
        string $broadcastIds,
        string $serviceIds,
        string $areWebcasts,
        DateTime $start,
        DateTime $end
    ) {
        $this->programmeItem = $programmeItem;
        $this->broadcastIds = $broadcastIds;
        $this->serviceIds = $serviceIds;
        $this->areWebcasts = $areWebcasts;
        $this->startAt = $start;
        $this->endAt = $end;
        $this->updateWebcastOnly();
        $this->updateDuration();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getBroadcastIds(): string
    {
        return $this->broadcastIds;
    }

    public function setBroadcastIds(string $broadcastIds): void
    {
        $this->broadcastIds = $broadcastIds;
    }

    public function getServiceIds(): string
    {
        return $this->serviceIds;
    }

    public function setServiceIds(string $serviceIds): void
    {
        $this->serviceIds = $serviceIds;
    }

    public function getAreWebcasts(): string
    {
        return $this->areWebcasts;
    }

    public function setAreWebcasts(string $areWebcasts)
    {
        $this->areWebcasts = $areWebcasts;
        $this->updateWebcastOnly();
    }

    public function getIsWebcastOnly(): bool
    {
        return $this->isWebcastOnly;
    }

    public function getProgrammeItem(): ?ProgrammeItem
    {
        return $this->programmeItem;
    }

    public function setProgrammeItem(ProgrammeItem $programmeItem): void
    {
        $this->programmeItem = $programmeItem;
    }

    public function getTleo(): ?Programme
    {
        return $this->tleo;
    }

    public function setTleo(?Programme $tleo)
    {
        $this->tleo = $tleo;
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

    private function updateWebcastOnly(): void
    {
        $this->isWebcastOnly = (strstr($this->areWebcasts, "0") === false);
    }

    private function updateDuration(): void
    {
        $this->duration = $this->endAt->getTimestamp() - $this->startAt->getTimestamp();
    }
}
