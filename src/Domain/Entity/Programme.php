<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use InvalidArgumentException;

abstract class Programme
{
    /**
     * @var Pid
     */
    protected $pid;

    /**
     * @var string
     */
    protected $title;

    /**
     * @var string
     */
    protected $searchTitle;

    /**
     * @var string
     */

    protected $shortSynopsis;

    /**
     * @var string
     */
    protected $longestSynopsis;

    /**
     * @var Image
     */
    protected $image;

    /**
     * @var int
     */
    protected $promotionsCount;

    /**
     * @var int
     */
    protected $relatedLinksCount;

    /**
     * @var bool
     */
    protected $hasSupportingContent;

    /**
     * @var bool
     */
    protected $isStreamable;

    /**
     * @var Programme|null
     */
    protected $parent;

    /**
     * @var PartialDate|null
     */
    protected $releaseDate;

    /**
     * @var int|null
     */
    protected $position;

    /**
     * @var MasterBrand|null
     */
    protected $masterBrand;

    /**
     * @var Genres[]
     */
    protected $genres;

    /**
     * @var Formats[]
     */
    protected $formats;

    /**
     * @var RelatedLink[]
     */
    protected $relatedLinks;

    public function __construct(
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
        PartialDate $releaseDate = null,
        int $position = null,
        MasterBrand $masterBrand = null,
        array $genres = [],
        array $formats = [],
        array $relatedLinks = []
    ) {
        $this->assertArrayOfType('genres', $genres, Genre::CLASS);
        $this->assertArrayOfType('formats', $formats, Format::CLASS);
        $this->assertArrayOfType('relatedLinks', $relatedLinks, RelatedLink::CLASS);

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
        $this->releaseDate = $releaseDate;
        $this->position = $position;
        $this->masterBrand = $masterBrand;
        $this->genres = $genres;
        $this->formats = $formats;
        $this->relatedLinks = $relatedLinks;
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
     * @return PartialDate|null
     */
    public function getReleaseDate()
    {
        return $this->releaseDate;
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
     * @return RelatedLink[]
     */
    public function getRelatedLinks(): array
    {
        return $this->relatedLinks;
    }

    /**
     * @return Network|null
     */
    public function getNetwork()
    {
        return $this->masterBrand->getNetwork() ?? null;
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
