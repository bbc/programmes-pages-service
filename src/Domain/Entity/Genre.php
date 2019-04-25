<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGenre;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;

class Genre extends Category
{
    /** @var string */
    private $hierarchicalTitle;

    /** @var Genre|null */
    private $parent;

    /** @var Genre[] */
    private $ancestry;

    /** @var string */
    private $urlKeyHierarchy;

    /** @var array|null */
    private $children;

    public function __construct(
        array $dbAncestryIds,
        string $id,
        string $title,
        string $urlKey,
        ?Genre $parent = null,
        ?array $children = null
    ) {
        parent::__construct($dbAncestryIds, $id, $title, $urlKey);
        $this->parent = $parent;
        $this->children = $children;
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
     * [Grandparent, Parent, Genre]
     *
     * @return Genre[]
     */
    public function getAncestry(): array
    {
        if ($this->ancestry === null) {
            $this->ancestry = [$currentGenre = $this];

            while ($currentGenre = $currentGenre->getParent()) {
                array_unshift($this->ancestry, $currentGenre);
            }
        }

        return $this->ancestry;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getChildren(): array
    {
        if ($this->children === null) {
            throw new DataNotFetchedException(
                'Could not get children of Genre "'
                . $this->getId() . '" as they were not fetched'
            );
        }

        return $this->children;
    }

    public function getTopLevel(): Genre
    {
        return $this->getAncestry()[0];
    }

    public function getHierarchicalTitle(): string
    {
        if ($this->hierarchicalTitle === null) {
            $this->hierarchicalTitle = '';

            $ancestry = $this->getAncestry();
            for ($i = 0, $l = count($ancestry); $i < $l; $i++) {
                $this->hierarchicalTitle .= ($i === 0 ? '' : ': ') . $ancestry[$i]->getTitle();
            }
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
            $this->urlKeyHierarchy = '';

            $ancestry = $this->getAncestry();
            for ($i = 0, $l = count($ancestry); $i < $l; $i++) {
                $this->urlKeyHierarchy .= ($i === 0 ? '' : '/') . $ancestry[$i]->getUrlKey();
            }
        }

        return $this->urlKeyHierarchy;
    }
}
