<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Genre extends Category
{
    /** @var Genre|null */
    private $parent;

    public function __construct(
        array $dbAncestryIds,
        string $id,
        string $title,
        string $urlKey,
        ?Genre $parent = null
    ) {
        parent::__construct($dbAncestryIds, $id, $title, $urlKey);
        $this->parent = $parent;
    }

    public function getParent(): ?Genre
    {
        return $this->parent;
    }
}
