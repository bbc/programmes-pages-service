<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="pips_backfill_processed_time_idx", columns={"processed_time"}),
 *     @ORM\Index(name="pips_backfill_locked_at_idx", columns={"locked_at"}),
 *     @ORM\Index(name="pips_backfill_locking_idx", columns={"processed_time","locked","cid"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PipsBackfillRepository")
 *
 * Note that the composite index here is useful to avoid locking. Don't ask. You don't want to know.
 */
class PipsBackfill extends PipsChangeBase
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint", nullable=false,  options={"unsigned"=true})
     */
    protected $cid;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    private $locked = false;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lockedAt;

    public function getLocked(): bool
    {
        return $this->locked;
    }

    public function setLocked(bool $locked)
    {
        $this->locked = $locked;
    }

    public function getLockedAt(): \DateTime
    {
        return $this->lockedAt;
    }

    /**
     * @param \DateTime|null $lockedAt
     */
    public function setLockedAt($lockedAt)
    {
        $this->lockedAt = $lockedAt;
    }
}
