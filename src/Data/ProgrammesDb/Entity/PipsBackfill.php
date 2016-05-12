<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="pips_backfill_processed_time_idx", columns={"processed_time"}),
 *     @ORM\Index(name="pips_backfill_locked_at_idx", columns={"locked_at"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PipsBackfillRepository")
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
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $lockedAt;

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
