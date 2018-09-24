<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use DateTimeImmutable;
use InvalidArgumentException;

abstract class Programme extends CoreEntity
{
    /** @var bool */
    private $hasSupportingContent;

    /** @var bool */
    private $isStreamable;

    /** @var bool */
    private $isStreamableAlternate;

    /** @var int|null */
    private $position;

    /** @var Genre[]|null */
    private $genres;

    /** @var Format[]|null */
    private $formats;

    /** @var DateTimeImmutable|null */
    private $firstBroadcastDate;

    /** @var int */
    private $aggregatedGalleriesCount;

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
        int $aggregatedGalleriesCount,
        Options $options,
        ?Programme $parent = null,
        ?int $position = null,
        ?MasterBrand $masterBrand = null,
        ?array $genres = null,
        ?array $formats = null,
        ?DateTimeImmutable $firstBroadcastDate = null
    ) {
        $this->assertArrayOfType('genres', $genres, Genre::class);
        $this->assertArrayOfType('formats', $formats, Format::class);

        parent::__construct(
            $dbAncestryIds,
            $pid,
            $title,
            $searchTitle,
            $synopses,
            $image,
            $promotionsCount,
            $relatedLinksCount,
            $contributionsCount,
            $options,
            $masterBrand
        );

        $this->hasSupportingContent = $hasSupportingContent;
        $this->isStreamable = $isStreamable;
        $this->isStreamableAlternate = $isStreamableAlternate;
        $this->parent = $parent;
        $this->position = $position;
        $this->genres = $genres;
        $this->formats = $formats;
        $this->firstBroadcastDate = $firstBroadcastDate;
        $this->aggregatedGalleriesCount = $aggregatedGalleriesCount;
    }

    public function hasSupportingContent(): bool
    {
        return $this->hasSupportingContent;
    }

    public function isStreamable(): bool
    {
        return $this->isStreamable;
    }

    public function isStreamableAlternate(): bool
    {
        return $this->isStreamableAlternate;
    }

    public function getPosition(): ?int
    {
        return $this->position;
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

    public function getFirstBroadcastDate(): ?DateTimeImmutable
    {
        return $this->firstBroadcastDate;
    }

    public function getAggregatedGalleriesCount(): int
    {
        return $this->aggregatedGalleriesCount;
    }

    /**
     * @param string $property
     * @param array|null $array
     * @param string $expectedType
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
}
