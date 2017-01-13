<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
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

    public function getDatabaseIdentifier(): string
    {
        return $this->databaseIdentifier;
    }

    public function getFirstSeenAt(): DateTime
    {
        return $this->firstSeenAt;
    }

    public function setFirstSeenAt(DateTime $firstSeenAt): void
    {
        $this->firstSeenAt = $firstSeenAt;
    }

    public function getLastPassOrFailLogged(): ?DateTime
    {
        return $this->lastPassOrFailLogged;
    }

    public function setLastPassOrFailLogged(DateTime $lastPassOrFailLogged): void
    {
        $this->lastPassOrFailLogged = $lastPassOrFailLogged;
    }

    public function getConsecutiveFailedHealthChecks(): int
    {
        return $this->consecutiveFailedHealthChecks;
    }

    public function setConsecutiveFailedHealthChecks(int $consecutiveFailedHealthChecks): void
    {
        $this->consecutiveFailedHealthChecks = $consecutiveFailedHealthChecks;
    }

    public function getConsecutivePassedHealthChecks(): int
    {
        return $this->consecutivePassedHealthChecks;
    }

    public function setConsecutivePassedHealthChecks(int $consecutivePassedHealthChecks): void
    {
        $this->consecutivePassedHealthChecks = $consecutivePassedHealthChecks;
    }

    public function getEndpoint(): string
    {
        return $this->endpoint;
    }

    public function setEndpoint(string $endpoint): void
    {
        $this->endpoint = $endpoint;
    }

    public function getPort(): string
    {
        return $this->port;
    }

    public function setPort(string $port): void
    {
        $this->port = $port;
    }

    public function getStatus(): string
    {
        return $this->status;
    }

    public function setStatus(string $status): void
    {
        $this->status = $status;
    }

    public function isInPool(): bool
    {
        return $this->inPool;
    }

    public function setInPool(bool $inPool): void
    {
        $this->inPool = $inPool;
    }

    public function getWeight(): int
    {
        return $this->weight;
    }

    public function setWeight(int $weight): void
    {
        $this->weight = $weight;
    }

    public function isDraining(): bool
    {
        return $this->draining;
    }
}
