<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="denorm_backfill_processed_time_idx", columns={"processed_time"}),
 *     @ORM\Index(name="denorm_backfill_locked_at_idx", columns={"locked_at"}),
 *     @ORM\Index(name="denorm_backfill_locking_idx", columns={"processed_time","locked","id"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\DenormBackfillRepository")
 *
 * Note that the composite index here is useful to avoid locking. Don't ask. You don't want to know.
 */
class DenormBackfill
{
    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\GeneratedValue(strategy="AUTO")
     * @ORM\Column(type="bigint", nullable=false,  options={"unsigned"=true})
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $createdTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    protected $processedTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $entityId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $action;

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


    public function getId(): int
    {
        return $this->id;
    }

    public function getCreatedTime(): \DateTime
    {
        return $this->createdTime;
    }

    public function setCreatedTime(\DateTime $createdTime)
    {
        $this->createdTime = $createdTime;
    }

    /**
     * @return \DateTime
     */
    public function getProcessedTime()
    {
        return $this->processedTime;
    }

    /**
     * @param \DateTime $processedTime
     */
    public function setProcessedTime($processedTime)
    {
        $this->processedTime = $processedTime;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function setEntityId(string $entityId)
    {
        $this->entityId = $entityId;
    }

    public function getAction(): string
    {
        return $this->action;
    }

    public function setAction(string $action)
    {
        $this->action = $action;
    }

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
