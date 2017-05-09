<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Lease
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     * @ORM\Id()
     */
    private $jobName;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=255, nullable=false)
     */
    private $workerId = 'unassigned';

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $leaseExpiration;

    public function __construct(string $jobName)
    {
        $this->jobName = $jobName;
        $this->leaseExpiration = new DateTime('now');
        $this->workerId = 'Unassigned';
    }

    public function getJobName(): string
    {
        return $this->jobName;
    }

    public function getWorkerId(): string
    {
        return $this->workerId;
    }

    public function setWorkerId(string $workerId)
    {
        $this->workerId = $workerId;
    }

    public function getLeaseExpiration(): DateTime
    {
        return $this->leaseExpiration;
    }

    public function setLeaseExpiration(DateTime $leaseExpiration)
    {
        $this->leaseExpiration = $leaseExpiration;
    }
}
