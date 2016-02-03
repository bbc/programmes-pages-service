<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait AvailableEpisodesCountTrait
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    protected $availableEpisodesCount = 0;

    public function getAvailableEpisodesCount(): int
    {
        return $this->availableEpisodesCount;
    }

    public function setAvailableEpisodesCount(int $availableEpisodesCount)
    {
        $this->availableEpisodesCount = $availableEpisodesCount;
    }
}
