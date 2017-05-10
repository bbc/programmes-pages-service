<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

trait IsPodcastableMethodsTrait
{
    public function getIsPodcastable(): bool
    {
        return $this->isPodcastable;
    }

    public function setIsPodcastable(bool $isPodcastable): void
    {
        $this->isPodcastable = $isPodcastable;
    }
}
