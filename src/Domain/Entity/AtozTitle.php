<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class AtozTitle
{
    /** @var string */
    private $title;

    /** @var string */
    private $firstLetter;

    /** @var CoreEntity */
    private $titledEntity;

    public function __construct(
        string $title,
        string $firstLetter,
        CoreEntity $titledEntity
    ) {
        $this->title = $title;
        $this->firstLetter = $firstLetter;
        $this->titledEntity = $titledEntity;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getFirstLetter(): string
    {
        return $this->firstLetter;
    }

    public function getTitledEntity(): CoreEntity
    {
        return $this->titledEntity;
    }
}
