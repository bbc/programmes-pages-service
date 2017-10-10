<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGenre;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;

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

    /**
     * @throws DataNotFetchedException
     */
    public function getParent(): ?Genre
    {
        if ($this->parent instanceof UnfetchedGenre) {
            throw new DataNotFetchedException(
                'Could not get Parent of Genre "'
                    . $this->getId() . '" as it was not fetched'
            );
        }

        return $this->parent;
    }

    /**
     * Given a Genre which has a Parent and GrandParent
     * Will return an array of Genres as such:
     *
     * [Genre, Parent, Grandparent]
     *
     * @return Genre[]
     */
    public function getAncestry(): array
    {
        $currentGenre = $this;
        $ancestry = [];

        do {
            $ancestry[] = $currentGenre;
        } while ($currentGenre = $currentGenre->getParent());


        return $ancestry;
    }

    /**
     * Given a Genre which has a Parent and GrandParent
     * Will return a urlKeyHierarchy as such:
     *
     * /GrandParentUrlKey/ParentUrlKey/GenreUrlKey
     *
     * @return string
     */
    public function getUrlKeyHierarchy(): string
    {
        $currentGenre = $this;
        $urlKeyHierarchy = [];

        do {
            array_unshift($urlKeyHierarchy, $currentGenre->getUrlKey());
        } while ($currentGenre = $currentGenre->getParent());

        return implode('/', $urlKeyHierarchy);
    }
}
