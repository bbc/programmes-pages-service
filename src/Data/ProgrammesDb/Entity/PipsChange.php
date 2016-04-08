<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * PipsChange
 *
 * @ORM\Table(name="pips_change")
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
     * @var string
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $createdTime;
    /**
     * @var string
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $processedTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $entityId;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
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
     * @ORM\Column(type="string", nullable=false)
     */
    private $status;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $type;

    public function getCid(): int
    {
        return $this->cid;
    }

    public function setCid(int $cid): PipsChange
    {
        $this->cid = $cid;

        return $this;
    }

    public function getCreatedTime(): \DateTime
    {
        return $this->createdTime;
    }

    public function setCreatedTime(\DateTime $createdTime): PipsChange
    {
        $this->createdTime = $createdTime;

        return $this;
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
     * @return PipsChange
     */
    public function setProcessedTime($processedTime): PipsChange
    {
        $this->processedTime = $processedTime;

        return $this;
    }

    public function getEntityId(): string
    {
        return $this->entityId;
    }

    public function setEntityId(string $entityId): PipsChange
    {
        $this->entityId = $entityId;

        return $this;
    }

    public function getEntityType(): string
    {
        return $this->entityType;
    }

    public function setEntityType(string $entityType): PipsChange
    {
        $this->entityType = $entityType;

        return $this;
    }

    public function getEntityUrl(): string
    {
        return $this->entityUrl;
    }

    public function setEntityUrl(string $entityUrl): PipsChange
    {
        $this->entityUrl = $entityUrl;

        return $this;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): PipsChange
    {
        $this->status = $status;

        return $this;
    }

    public function getType(): string
    {
        return $this->type;
    }

    public function setType(string $type): PipsChange
    {
        $this->type = $type;

        return $this;
    }
}
