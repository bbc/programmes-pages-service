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
     * @ORM\Column(type="string", nullable=true)
     */
    private $latestChangeEventId;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $latestChangeEventCreatedAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $latestChangeEventProcessedAt;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $pipsLatestId;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $pipsLatestTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $nitroLastMessageId;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $nitroLastMessageTime;

    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=true)
     */
    private $nitroLatestPid;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $nitroLatestTime;

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

    public function getLatestChangeEventCreatedAt(): DateTime
    {
        return $this->latestChangeEventCreatedAt;
    }

    public function setLatestChangeEventCreatedAt(DateTime $latestChangeEventCreatedAt)
    {
        $this->latestChangeEventCreatedAt = $latestChangeEventCreatedAt;
    }

    public function getLatestChangeEventProcessedAt(): DateTime
    {
        return $this->latestChangeEventProcessedAt;
    }

    public function setLatestChangeEventProcessedAt(DateTime $latestChangeEventProcessedAt)
    {
        $this->latestChangeEventProcessedAt = $latestChangeEventProcessedAt;
    }

    public function getPipsLatestId(): DateTime
    {
        return $this->pipsLatestId;
    }

    public function setPipsLatestId(string $pipsLatestId)
    {
        $this->pipsLatestId = $pipsLatestId;
    }

    public function getPipsLatestTime(): DateTime
    {
        return $this->pipsLatestTime;
    }

    public function setPipsLatestTime(DateTime $pipsLatestTime)
    {
        $this->pipsLatestTime = $pipsLatestTime;
    }

    public function getNitroLastMessageId(): string
    {
        return $this->nitroLastMessageId;
    }

    public function setNitroLastMessageId(string $nitroLastMessageId)
    {
        $this->nitroLastMessageId = $nitroLastMessageId;
    }

    public function getNitroLastMessageTime(): DateTime
    {
        return $this->nitroLastMessageTime;
    }

    public function setNitroLastMessageTime(DateTime $nitroLastMessageTime)
    {
        $this->nitroLastMessageTime = $nitroLastMessageTime;
    }

    public function getNitroLatestPid(): string
    {
        return $this->nitroLatestPid;
    }

    public function setNitroLatestPid(string $nitroLatestPid)
    {
        $this->nitroLatestPid = $nitroLatestPid;
    }

    public function getNitroLatestTime(): DateTime
    {
        return $this->nitroLatestTime;
    }

    public function setNitroLatestTime(DateTime $nitroLatestTime)
    {
        $this->nitroLatestTime = $nitroLatestTime;
    }
}
