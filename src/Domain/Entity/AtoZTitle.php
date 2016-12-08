<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class AtoZTitle
{
    /** @var string */
    private $title;

    /** @var string */
    private $firstLetter;

    /** @var Programme */
    private $coreEntity;

    public function __construct(
        string $title,
        string $firstLetter,
        Programme $coreEntity // @TODO group models
    ) {
        $this->title = $title;
        $this->firstLetter = $firstLetter;
        $this->coreEntity = $coreEntity;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFirstLetter(): string
    {
        return $this->firstLetter;
    }

    public function getCoreEntity(): Programme
    {
        return $this->coreEntity;
    }
}
