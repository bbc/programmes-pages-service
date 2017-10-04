<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces;

interface GalleriesCountableInterface
{

    public function getAggregatedGalleriesCount(): int;

    public function setAggregatedGalleriesCount(int $aggregatedGalleriesCount): void;
}
