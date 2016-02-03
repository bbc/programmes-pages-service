<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Mapping\Annotation as Gedmo;
use DateTime;

/**
 * @ORM\Table()
 * @ORM\Entity
 */
class Status
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
    private $latestChangeEventId;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $latestChangeEventCreatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $latestChangeEventProcessedAt;


    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    private $pipsLatestId;


    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $pipsLatestTime;


    /**
     * @return int|null
     */
    public function getId()
    {
        return $this->id;
    }

    public function getLatestChangeEventId(): int
    {
        return $this->latestChangeEventId;
    }

    public function setLatestChangeEventId(int $latestChangeEventId)
    {
        $this->latestChangeEventId = $latestChangeEventId;
    }

    /**
     * @return DateTime
     */
    public function getLatestChangeEventCreatedAt()
    {
        return $this->latestChangeEventCreatedAt;
    }

    /**
     * @param DateTime $latestChangeEventCreatedAt
     */
    public function setLatestChangeEventCreatedAt($latestChangeEventCreatedAt)
    {
        $this->latestChangeEventCreatedAt = $latestChangeEventCreatedAt;
    }

    /**
     * @return DateTime
     */
    public function getLatestChangeEventProcessedAt()
    {
        return $this->latestChangeEventProcessedAt;
    }

    /**
     * @param DateTime $latestChangeEventProcessedAt
     */
    public function setLatestChangeEventProcessedAt($latestChangeEventProcessedAt)
    {
        $this->latestChangeEventProcessedAt = $latestChangeEventProcessedAt;
    }

    /**
     * @return DateTime
     */
    public function getPipsLatestId()
    {
        return $this->pipsLatestId;
    }

    /**
     * @param string $pipsLatestId
     */
    public function setPipsLatestId($pipsLatestId)
    {
        $this->pipsLatestId = $pipsLatestId;
    }

    /**
     * @return DateTime
     */
    public function getPipsLatestTime()
    {
        return $this->pipsLatestTime;
    }

    /**
     * @param DateTime $pipsLatestTime
     */
    public function setPipsLatestTime($pipsLatestTime)
    {
        $this->pipsLatestTime = $pipsLatestTime;
    }
}
