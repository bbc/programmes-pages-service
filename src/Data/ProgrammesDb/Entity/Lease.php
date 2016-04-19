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
     * @var int|null
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
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
        $this->leaseExpiration = new DateTime('now');
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
