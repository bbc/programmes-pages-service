<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 * @ORM\Table(indexes={
 *   @ORM\Index(name="database_replica_cluster_index", columns={"cluster"}),
 *   @ORM\Index(name="database_replica_cluster_inpool", columns={"in_pool"}),
 *   @ORM\Index(name="database_replica_cluster_draining", columns={"draining"}),
 * })
 */
class RdsInstance
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @ORM\Id()
     */
    private $databaseIdentifier;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $cluster;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $firstSeenAt;

    /**
     * @var \DateTime
     * @ORM\Column(type="datetime", nullable = true)
     */
    private $lastPassOrFailLogged;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $consecutiveFailedHealthChecks = 0;

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $consecutivePassedHealthChecks = 0;

    /**
     * @var string
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $endpoint;

    /**
     * @var string
     * @ORM\Column(type="string", length=16, nullable=false)
     */
    private $port;

    /**
     * @var string
     * @ORM\Column(type="string", length=32, nullable=false)
     */
    private $status = '';

    /**
     * @var int
     * @ORM\Column(type="integer", nullable=false)
     */
    private $weight = 0;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    private $inPool = false;

    /**
     * @var bool
     * @ORM\Column(type="boolean", nullable=false, options={"default" = 0})
     */
    private $draining = false;

    public function __construct(string $cluster, string $databaseIdentifier, string $endpoint, string $port, \DateTime $firstSeenAt)
    {
        $this->cluster = $cluster;
        $this->databaseIdentifier = $databaseIdentifier;
        $this->firstSeenAt = $firstSeenAt;
        $this->endpoint = $endpoint;
        $this->port = $port;
    }

    public function getCluster(): string
    {
        return $this->cluster;
    }

    /**
     * @return string
     */
    public function getDatabaseIdentifier(): string
    {
        return $this->databaseIdentifier;
    }

    public function getFirstSeenAt(): \DateTime
    {
        return $this->firstSeenAt;
    }

    public function setFirstSeenAt(\DateTime $firstSeenAt)
    {
        $this->firstSeenAt = $firstSeenAt;
    }

    /**
     * @return \DateTime|null
     */
    public function getLastPassOrFailLogged()
    {
        return $this->lastPassOrFailLogged;
    }

    /**
     * @param \DateTime $lastPassOrFailLogged
     */
    public function setLastPassOrFailLogged(\DateTime $lastPassOrFailLogged)
    {
        $this->lastPassOrFailLogged = $lastPassOrFailLogged;
    }

    /**
     * @return int
     */
    public function getConsecutiveFailedHealthChecks(): int
    {
        return $this->consecutiveFailedHealthChecks;
    }

    /**
     * @param int $consecutiveFailedHealthChecks
     */
    public function setConsecutiveFailedHealthChecks(int $consecutiveFailedHealthChecks)
    {
        $this->consecutiveFailedHealthChecks = $consecutiveFailedHealthChecks;
    }

    /**
     * @return int
     */
    public function getConsecutivePassedHealthChecks(): int
    {
        return $this->consecutivePassedHealthChecks;
    }

    /**
     * @param int $consecutivePassedHealthChecks
     */
    public function setConsecutivePassedHealthChecks(int $consecutivePassedHealthChecks)
    {
        $this->consecutivePassedHealthChecks = $consecutivePassedHealthChecks;
    }

    /**
     * @return string
     */
    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    /**
     * @param string $endpoint
     */
    public function setEndpoint(string $endpoint)
    {
        $this->endpoint = $endpoint;
    }

    /**
     * @return string
     */
    public function getPort(): string
    {
        return $this->port;
    }

    /**
     * @param string $port
     */
    public function setPort(string $port)
    {
        $this->port = $port;
    }

    /**
     * @return string
     */
    public function getStatus(): string
    {
        return $this->status;
    }

    /**
     * @param string $status
     */
    public function setStatus(string $status)
    {
        $this->status = $status;
    }

    /**
     * @return boolean
     */
    public function isInPool(): bool
    {
        return $this->inPool;
    }

    /**
     * @param boolean $inPool
     */
    public function setInPool(bool $inPool)
    {
        $this->inPool = $inPool;
    }

    /**
     * @return int
     */
    public function getWeight(): int
    {
        return $this->weight;
    }

    /**
     * @param int $weight
     */
    public function setWeight(int $weight)
    {
        $this->weight = $weight;
    }

    /**
     * @return boolean
     */
    public function isDraining(): bool
    {
        return $this->draining;
    }
}
