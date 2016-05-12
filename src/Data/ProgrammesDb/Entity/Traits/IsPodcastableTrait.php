<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use Doctrine\ORM\Mapping as ORM;

trait IsPodcastableTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    protected $isPodcastable = false;

    public function getIsPodcastable(): bool
    {
        return $this->isPodcastable;
    }

    public function setIsPodcastable(bool $isPodcastable)
    {
        $this->isPodcastable = $isPodcastable;
    }
}
