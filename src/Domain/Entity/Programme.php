<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use InvalidArgumentException;

abstract class Programme
{
    /**
     * @var int
     */
    private $dbId;

    /**
     * @var Pid
     */
    private $pid;

    /**
     * @var string
     */
    private $title;

    /**
     * @var string
     */
    private $searchTitle;

    /**
     * @var string
     */
    private $shortSynopsis;

    /**
     * @var string
     */
    private $longestSynopsis;

    /**
     * @var Image
     */
    private $image;

    /**
     * @var int
     */
    private $promotionsCount;

    /**
     * @var int
     */
    private $relatedLinksCount;

    /**
     * @var bool
     */
    private $hasSupportingContent;

    /**
     * @var bool
     */
    private $isStreamable;

    /**
     * @var Programme|null
     */
    private $parent;

    /**
     * @var int|null
     */
    private $position;

    /**
     * @var MasterBrand|null
     */
    private $masterBrand;

    /**
     * @var Genre[]
     */
    private $genres;

    /**
     * @var Format[]
     */
    private $formats;

    public function __construct(
        int $dbId,
        Pid $pid,
        string $title,
        string $searchTitle,
        string $shortSynopsis,
        string $longestSynopsis,
        Image $image,
        int $promotionsCount,
        int $relatedLinksCount,
        bool $hasSupportingContent,
        bool $isStreamable,
        Programme $parent = null,
        int $position = null,
        MasterBrand $masterBrand = null,
        array $genres = [],
        array $formats = []
    ) {
        $this->assertArrayOfType('genres', $genres, Genre::CLASS);
        $this->assertArrayOfType('formats', $formats, Format::CLASS);

        $this->dbId = $dbId;
        $this->pid = $pid;
        $this->title = $title;
        $this->searchTitle = $searchTitle;
        $this->shortSynopsis = $shortSynopsis;
        $this->longestSynopsis = $longestSynopsis;
        $this->image = $image;
        $this->promotionsCount = $promotionsCount;
        $this->relatedLinksCount = $relatedLinksCount;
        $this->hasSupportingContent = $hasSupportingContent;
        $this->isStreamable = $isStreamable;
        $this->parent = $parent;
        $this->position = $position;
        $this->masterBrand = $masterBrand;
        $this->genres = $genres;
        $this->formats = $formats;
    }

    /**
     * Database ID. Yes, this is a leaky abstraction as Database Ids are
     * implementation details of how we're storing data, rather than anything
     * intrinsic to a PIPS entity. However if we keep it pure then when we look
     * up things like "All related links that belong to a Programme" then we
     * have to use the Programme PID as the key, which requires a join to the
     * CoreEntity table. This join can be avoided if we already know the Foreign
     * Key value on the Related Links table (i.e. the Programme ID field).
     * Removing these joins shall result in faster DB queries which is more
     * important that keeping a pure Domain model.
     */
    public function getDbId(): int
    {
        return $this->dbId;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getSearchTitle(): string
    {
        return $this->searchTitle;
    }

    public function getShortSynopsis(): string
    {
        return $this->shortSynopsis;
    }

    public function getLongestSynopsis(): string
    {
        return $this->longestSynopsis;
    }

    public function getImage(): Image
    {
        return $this->image;
    }

    public function getPromotionsCount(): int
    {
        return $this->promotionsCount;
    }

    public function getRelatedLinksCount(): int
    {
        return $this->relatedLinksCount;
    }

    public function hasSupportingContent(): bool
    {
        return $this->hasSupportingContent;
    }

    public function isStreamable(): bool
    {
        return $this->isStreamable;
    }

    /**
     * @return Programme|null
     */
    public function getParent()
    {
        return $this->parent;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    /**
     * @return MasterBrand|null
     */
    public function getMasterBrand()
    {
        return $this->masterBrand;
    }

    /**
     * @return Genre[]
     */
    public function getGenres(): array
    {
        return $this->genres;
    }

    /**
     * @return Format[]
     */
    public function getFormats(): array
    {
        return $this->formats;
    }

    /**
     * @return Network|null
     */
    public function getNetwork()
    {
        return $this->masterBrand ? $this->masterBrand->getNetwork() : null;
    }

    private function assertArrayOfType($property, $array, $expectedType)
    {
        foreach ($array as $item) {
            if (!$item instanceof $expectedType) {
                throw new InvalidArgumentException(sprintf(
                    'Tried to create a Programme with invalid %s. Expected an array of %s but the array contained an instance of "%s"',
                    $property,
                    $expectedType,
                    (is_object($item) ? get_class($item) : gettype($item))
                ));
            }
        }

        return true;
    }
}
