<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGenre;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;

class Genre extends Category
{
    /** @var string|null */
    private $hierarchicalTitle;

    /** @var Genre|null */
    private $parent;

    /** @var array|null */
    private $ancestry;

    /** @var string|null */
    private $urlKeyHierarchy;

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
        $this->initAncestry();

        return $this->ancestry;
    }

    public function getTopLevel(): Genre
    {
        $this->initAncestry();

        return end($this->ancestry);
    }

    public function getHierarchicalTitle(): string
    {
        if ($this->hierarchicalTitle === null) {
            $ancestor = $this->getTopLevel();
            $first = true;

            do {
                $this->hierarchicalTitle .= ($first ? '' : ': ') . $ancestor->getTitle();
                $first = false;
            } while ($ancestor = prev($this->ancestry));
        }

        return $this->hierarchicalTitle;
    }

    /**
     * Given a Genre which has a Parent and GrandParent
     * Will return a urlKeyHierarchy as such:
     *
     * GrandParentUrlKey/ParentUrlKey/GenreUrlKey
     */
    public function getUrlKeyHierarchy(): string
    {
        if ($this->urlKeyHierarchy === null) {
            $ancestor = $this->getTopLevel();
            $first = true;

            do {
                $this->urlKeyHierarchy .= ($first ? '' : '/') . $ancestor->getUrlKey();
                $first = false;
            } while ($ancestor = prev($this->ancestry));
        }

        return $this->urlKeyHierarchy;
    }

    private function initAncestry()
    {
        if ($this->ancestry === null) {
            $currentGenre = $this;

            do {
                $this->ancestry[] = $currentGenre;
            } while ($currentGenre = $currentGenre->getParent());
        }
    }
}
