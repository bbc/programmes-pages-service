<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AvailableClipsCountMethodsTrait
{
    public function getAvailableClipsCount(): int
    {
        return $this->availableClipsCount;
    }

    public function setAvailableClipsCount(int $availableClipsCount)
    {
        $this->availableClipsCount = $availableClipsCount;
    }
}
