<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces;

interface EpisodeCountableInterface
{

    public function setAggregatedEpisodesCount(int $count);

    public function getAncestry();
}
