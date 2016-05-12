<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * @ORM\Entity()
 */
class Lease
{

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     */
    private $id;

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

    public function __construct()
    {
        $this->id = 1;
        $this->leaseExpiration = new DateTime('now');
        $this->workerId = 'Unassigned';
    }

    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
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
