<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

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
