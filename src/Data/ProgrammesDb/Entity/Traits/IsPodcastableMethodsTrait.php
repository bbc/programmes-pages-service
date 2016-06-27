<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IsPodcastableMethodsTrait
{
    public function getIsPodcastable(): bool
    {
        return $this->isPodcastable;
    }

    public function setIsPodcastable(bool $isPodcastable)
    {
        $this->isPodcastable = $isPodcastable;
    }
}
