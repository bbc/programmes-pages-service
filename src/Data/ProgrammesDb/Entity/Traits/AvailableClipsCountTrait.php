<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AvailableClipsCountTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $availableClipsCount = 0;

    public function getAvailableClipsCount(): int
    {
        return $this->availableClipsCount;
    }

    public function setAvailableClipsCount(int $availableClipsCount)
    {
        $this->availableClipsCount = $availableClipsCount;
    }
}
