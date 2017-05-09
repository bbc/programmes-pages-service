<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

trait AvailableGalleriesCountMethodsTrait
{
    public function getAvailableGalleriesCount(): int
    {
        return $this->availableGalleriesCount;
    }

    public function setAvailableGalleriesCount(int $availableGalleriesCount)
    {
        $this->availableGalleriesCount = $availableGalleriesCount;
    }
}
