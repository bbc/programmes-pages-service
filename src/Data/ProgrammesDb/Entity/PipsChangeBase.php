<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass
 */
abstract class PipsChangeBase
{
    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="bigint", nullable=false,  options={"unsigned"=true})
     */
    protected $cid;

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
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    protected $entityType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    protected $entityUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    protected $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    protected $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    protected $queuedAt;

    public function __construct()
    {
        $this->queuedAt = new \DateTime();
    }

    public function getCid(): int
    {
        return $this->cid;
    }

    public function setCid(int $cid): void
    {
        $this->cid = $cid;
    }

    public function getCreatedTime(): DateTime
    {
        return $this->createdTime;
    }

    public function setCreatedTime(DateTime $createdTime): void
    {
        $this->createdTime = $createdTime;
    }

    /**
     * @return DateTime|null
     */
    public function getProcessedTime()
    {
        return $this->processedTime;
    }

    public function setProcessedTime(DateTime $processedTime): void
    {
        $this->processedTime = $processedTime;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function setEntityId(string $entityId): void
    {
        $this->entityId = $entityId;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): void
    {
        $this->entityType = $entityType;
    }

    public function getEntityUrl(): string
    {
        return $this->entityUrl;
    }

    public function setEntityUrl(string $entityUrl): void
    {
        $this->entityUrl = $entityUrl;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): void
    {
        $this->type = $type;
    }

    public function getQueuedAt(): DateTime
    {
        return $this->queuedAt;
    }
}
