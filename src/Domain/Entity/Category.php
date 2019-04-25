<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

abstract class Category
{
    /** @var int[] */
    private $dbAncestryIds;

    /** @var string */
    private $id;

    /** @var string */
    private $title;

    /** @var string */
    private $urlKey;

    public function __construct(
        array $dbAncestryIds,
        string $id,
        string $title,
        string $urlKey
    ) {
        $this->dbAncestryIds = $dbAncestryIds;
        $this->id = $id;
        $this->title = $title;
        $this->urlKey = $urlKey;
    }

    abstract public function getChildren(): array;

    public function getId(): string
    {
        return $this->id;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrlKey(): string
    {
        return $this->urlKey;
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

    abstract protected function getAncestry(): array;

    abstract protected function getHierarchicalTitle(): string;

    abstract protected function getUrlKeyHierarchy(): string;
}
