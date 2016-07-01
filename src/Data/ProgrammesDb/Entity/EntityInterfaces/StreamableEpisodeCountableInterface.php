<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces;

interface StreamableEpisodeCountableInterface
{

    public function getAvailableEpisodesCount(): int;

    public function setAvailableEpisodesCount(int $availableEpisodesCount);

    public function getAncestry();
}
