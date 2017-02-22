<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * CollapsedBroadcasts are a combination of multiple Broadcasts pushed into a
 * a single entity, based upon the ProgrameItem being broadcast and the startAt
 * date. For instance if a Programme was broadcast on bbc_one_london and
 * bbc_one_yorkshire starting at the same time there shall be a single
 * CollapsedBroadcast representing that. This also applies across networks, so
 * if something was broadcast on bbc_radio_foyle and bbc_radio_ulster at the
 * same time then they are stored as a single Entity.
 *
 * As we group by ProgrammeItem and StartAt, this means that Broadcasts of
 * multiple versions that may have different durations, and thus different
 * endAt times (e.g. an Original and Shortened Version) will be rolled up into
 * a single CollapsedBroadcast. In this case we shall store the end_at and
 * duration of the longest Version.
 *
 * @ORM\Table(
 *   uniqueConstraints={@ORM\UniqueConstraint(name="collapsed_broadcast_unique_groupby", columns={"start_at", "programme_item_id"})},
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
     * This is not guaranteed to be the same for all Versions that were
     * broadcast as we group by Programme, not Version. This means that two
     * Versions of the same Programme that have different durations (e.g
     * the Original and a Shortened version) may be broadcast at the same time
     * and we roll them up into a single CollapsedBroadcast.
     * We prefer the Version with furthest away endAt. i.e. the longest
     * Broadcast.
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $endAt;

    /**
     * @var int|null
     *
     * This is not guaranteed to be the same for all Versions that were
     * broadcast as we group by Programme, not Version. This means that two
     * Versions of the same Programme that have different durations (e.g
     * the Original and a Shortened version) may be broadcast at the same time
     * and we roll them up into a single CollapsedBroadcast.
     * We prefer the Version with furthest away endAt. i.e. the longest
     * Broadcast.
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
     * This value is true if any of the values for the group are true.
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

    public function setAreWebcasts(string $areWebcasts): void
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

    public function setTleo(?Programme $tleo): void
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
        $this->isWebcastOnly = (strpos($this->areWebcasts, "0") === false);
    }

    private function updateDuration(): void
    {
        $this->duration = $this->endAt->getTimestamp() - $this->startAt->getTimestamp();
    }
}
