<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;

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
        int $position = null
    ) {
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
}
