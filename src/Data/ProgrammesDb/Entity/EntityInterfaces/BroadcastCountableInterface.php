<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces;

interface BroadcastCountableInterface
{

    public function getAggregatedBroadcastsCount(): int;

    public function setAggregatedBroadcastsCount(int $aggregatedBroadcastsCount);
}
