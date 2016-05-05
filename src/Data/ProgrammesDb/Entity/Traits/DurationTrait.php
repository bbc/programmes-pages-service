<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use DateTime;
use Doctrine\ORM\Mapping as ORM;

trait DurationTrait
{

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $startAt;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=false)
     */
    private $endAt;

    /**
     * @var int|null
     * @ORM\Column(type="integer", nullable=false)
     */
    private $duration;

    public function getStart(): DateTime
    {
        return $this->startAt;
    }

    public function setStart(DateTime $start)
    {
        $this->startAt = $start;
        $this->updateDuration();
    }

    public function getEnd(): DateTime
    {
        return $this->endAt;
    }

    public function setEnd(DateTime $end)
    {
        $this->endAt = $end;
        $this->updateDuration();
    }

    public function getDuration(): int
    {
        return $this->duration;
    }

    public function setDuration(int $duration)
    {
        $this->duration = $duration;
    }

    protected function updateDuration()
    {
        if ($this->startAt instanceof DateTime && $this->endAt instanceof DateTime) {
            $this->setDuration($this->endAt->getTimestamp() - $this->startAt->getTimestamp());
        }
    }
}
