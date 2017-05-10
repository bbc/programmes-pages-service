<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class AtozTitle
{
    /** @var string */
    private $title;

    /** @var string */
    private $firstLetter;

    /** @var Programme */
    private $titledEntity;

    /**
     * @TODO $titledEntity is programmes only but eventually we should support
     * groups too
     */
    public function __construct(
        string $title,
        string $firstLetter,
        Programme $titledEntity
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

    public function getTitledEntity(): Programme
    {
        return $this->titledEntity;
    }
}
