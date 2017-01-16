<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;
use InvalidArgumentException;

abstract class Programme
{
    /** @var int[]*/
    private $dbAncestryIds;

    /** @var Pid */
    private $pid;

    /** @var string */
    private $title;

    /** @var string */
    private $searchTitle;

    /** @var Synopses */
    private $synopses;

    /** @var Image */
    private $image;

    /** @var int */
    private $promotionsCount;

    /** @var int */
    private $relatedLinksCount;

    /** @var int */
    private $contributionsCount;

    /** @var bool */
    private $hasSupportingContent;

    /** @var bool */
    private $isStreamable;

    /** @var bool */
    private $isStreamableAlternatate;

    /** @var Programme|null */
    private $parent;

    /** @var int|null */
    private $position;

    /** @var MasterBrand|null */
    private $masterBrand;

    /** @var Genre[]|null */
    private $genres;

    /** @var Format[]|null */
    private $formats;

    /** @var DateTimeImmutable */
    private $firstBroadcastDate;

    public function __construct(
        array $dbAncestryIds,
        Pid $pid,
        string $title,
        string $searchTitle,
        Synopses $synopses,
        Image $image,
        int $promotionsCount,
        int $relatedLinksCount,
        bool $hasSupportingContent,
        bool $isStreamable,
        bool $isStreamableAlternate,
        int $contributionsCount,
        ?Programme $parent = null,
        ?int $position = null,
        ?MasterBrand $masterBrand = null,
        ?array $genres = null,
        ?array $formats = null,
        ?DateTimeImmutable $firstBroadcastDate = null
    ) {
        $this->assertAncestry($dbAncestryIds);
        $this->assertArrayOfType('genres', $genres, Genre::CLASS);
        $this->assertArrayOfType('formats', $formats, Format::CLASS);

        $this->dbAncestryIds = $dbAncestryIds;
        $this->pid = $pid;
        $this->title = $title;
        $this->searchTitle = $searchTitle;
        $this->synopses = $synopses;
        $this->image = $image;
        $this->promotionsCount = $promotionsCount;
        $this->relatedLinksCount = $relatedLinksCount;
        $this->hasSupportingContent = $hasSupportingContent;
        $this->isStreamable = $isStreamable;
        $this->isStreamableAlternatate = $isStreamableAlternate;
        $this->parent = $parent;
        $this->position = $position;
        $this->masterBrand = $masterBrand;
        $this->genres = $genres;
        $this->formats = $formats;
        $this->firstBroadcastDate = $firstBroadcastDate;
        $this->contributionsCount = $contributionsCount;
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
     * important than keeping a pure Domain model.
     */
    public function getDbId(): int
    {
        return end($this->dbAncestryIds);
    }

    /**
     * Database Ancestry IDs. Yes, this is a leaky abstraction (see above).
     * However it is useful to know the full ancestry if we want to make queries
     * searching through all descendants like "All Clips underneath a Brand at
     * any level, not just immediate children". This saves joining the
     * CoreEntity table to itself which is expensive.
     *
     * @return int[]
     */
    public function getDbAncestryIds(): array
    {
        return $this->dbAncestryIds;
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

    public function getSynopses(): Synopses
    {
        return $this->synopses;
    }

    public function getShortSynopsis(): string
    {
        return $this->synopses->getShortSynopsis();
    }

    public function getLongestSynopsis(): string
    {
        return $this->synopses->getLongestSynopsis();
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

    public function getContributionsCount(): int
    {
        return $this->contributionsCount;
    }

    public function hasSupportingContent(): bool
    {
        return $this->hasSupportingContent;
    }

    public function isStreamable(): bool
    {
        return $this->isStreamable;
    }

    public function isStreamableAlternatate(): bool
    {
        return $this->isStreamableAlternatate;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getParent(): ?Programme
    {
        if ($this->parent instanceof UnfetchedProgramme) {
            throw new DataNotFetchedException(
                'Could not get Parent of Programme "' . $this->pid . '" as it was not fetched'
            );
        }

        return $this->parent;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    /**
     * @throws DataNotFetchedException
     */
    public function getMasterBrand(): ?MasterBrand
    {
        if ($this->masterBrand instanceof UnfetchedMasterBrand) {
            throw new DataNotFetchedException(
                'Could not get MasterBrand of Programme "' . $this->pid . '" as it was not fetched'
            );
        }
        return $this->masterBrand;
    }

    /**
     * @return Genre[]
     * @throws DataNotFetchedException
     */
    public function getGenres(): array
    {
        if (is_null($this->genres)) {
            throw new DataNotFetchedException('Could not get Genres of Programme "' . $this->pid . '" as they were not fetched');
        }

        return $this->genres;
    }

    /**
     * @return Format[]
     * @throws DataNotFetchedException
     */
    public function getFormats(): array
    {
        if (is_null($this->formats)) {
            throw new DataNotFetchedException('Could not get Formats of Programme "' . $this->pid . '" as they were not fetched');
        }
        return $this->formats;
    }

    public function getNetwork(): ?Network
    {
        return $this->masterBrand ? $this->masterBrand->getNetwork() : null;
    }

    public function getFirstBroadcastDate(): ?DateTimeImmutable
    {
        return $this->firstBroadcastDate;
    }

    public function getTleo(): Programme
    {
        $parent = $this->getParent();
        if ($parent) {
            return $parent->getTleo();
        }
        return $this;
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertArrayOfType(string $property, ?array $array, string $expectedType): void
    {
        if (is_null($array)) {
            return;
        }

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
    }

    /**
     * @throws InvalidArgumentException
     */
    private function assertAncestry(array $array): void
    {
        if (empty($array)) {
            throw new InvalidArgumentException('Tried to create a Programme with invalid ancestry. Expected a non-empty array of integers but the array was empty');
        }

        foreach ($array as $item) {
            if (!is_int($item)) {
                throw new InvalidArgumentException(sprintf(
                    'Tried to create a Programme with invalid ancestry. Expected a non-empty array of integers but the array contained an instance of "%s"',
                    (is_object($item) ? get_class($item) : gettype($item))
                ));
            }
        }
    }
}
