<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AvailableEpisodesCountMethodsTrait
{
    public function getAvailableEpisodesCount(): int
    {
        return $this->availableEpisodesCount;
    }

    public function setAvailableEpisodesCount(int $availableEpisodesCount)
    {
        $this->availableEpisodesCount = $availableEpisodesCount;
    }
}
