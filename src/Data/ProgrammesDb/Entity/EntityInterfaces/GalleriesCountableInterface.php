<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces;

interface GalleriesCountableInterface
{

    public function getAvailableGalleriesCount(): int;

    public function setAvailableGalleriesCount(int $availableGalleriesCount);
}
