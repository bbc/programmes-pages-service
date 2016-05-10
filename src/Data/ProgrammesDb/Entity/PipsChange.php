<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Table(indexes={
 *     @ORM\Index(name="processed_time_idx", columns={"processed_time"}),
 * })
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PipsChangeRepository")
 */
class PipsChange
{

    /**
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="guid", nullable=false)
     */
    private $cid;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdTime;

    /**
     * @var \DateTime|null
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $processedTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=15, nullable=false)
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=64, nullable=false)
     */
    private $entityType;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $entityUrl;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $type;

    /**
     * @var \DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $queuedAt;

    public function __construct()
    {
        $this->queuedAt = new \DateTime();
    }

    public function getCid(): int
    {
        return $this->cid;
    }

    public function setCid(int $cid)
    {
        $this->cid = $cid;
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

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType)
    {
        $this->entityType = $entityType;
    }

    public function getEntityUrl(): string
    {
        return $this->entityUrl;
    }

    public function setEntityUrl(string $entityUrl)
    {
        $this->entityUrl = $entityUrl;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type)
    {
        $this->type = $type;
    }

    public function getQueuedAt(): \DateTime
    {
        return $this->queuedAt;
    }
}
