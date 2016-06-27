<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

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
