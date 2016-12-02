<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Genre extends Category
{
    /**
     * @var int[]
     */
    private $dbAncestryIds;

    /**
     * @var Genre|null
     */
    private $parent;

    public function __construct(
        array $dbAncestryIds,
        string $id,
        string $title,
        string $urlKey,
        Genre $parent = null
    ) {
        parent::__construct($id, $title, $urlKey);
        $this->parent = $parent;
        $this->dbAncestryIds = $dbAncestryIds;
    }

    /**
     * @return Genre|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * Database ID. Yes, this is a leaky abstraction as Database Ids are
     * implementation details of how we're storing data, rather than anything
     * intrinsic to a PIPS entity. However if we keep it pure then when we look
     * up things like "All subcategories of a category" then we
     * have to use the Category Pip ID as the key, which requires a join to the
     * Category table. This join can be avoided if we already know the parent DB id.
     * Removing these joins shall result in faster DB queries which is more
     * important than keeping a pure Domain model.
     */
    public function getDbId(): int
    {
        return end($this->dbAncestryIds);
    }

    /**
     * Database Ancestry IDs. Yes, this is a leaky abstraction (see above).
     * However it is useful to know the full ancestry if we want to make queries
     * searching through all descendants. This saves joining the
     * Category table to itself which is expensive.
     *
     * @return int[]
     */
    public function getDbAncestryIds(): array
    {
        return $this->dbAncestryIds;
    }
}
