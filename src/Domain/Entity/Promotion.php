<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use InvalidArgumentException;

class Promotion
{
    /** @var Pid */
    private $pid;

    /** @var PromotableInterface */
    private $promotedEntity;

    /** @var Synopses */
    private $synopses;

    /** @var string */
    private $title;

    /** @var string */
    private $url;

    /** @var int */
    private $weighting;

    /** @var bool */
    private $isSuperPromotion;

    /** @var RelatedLink[]|null */
    private $relatedLinks;

    public function __construct(
        Pid $pid,
        PromotableInterface $promotedEntity,
        string $title,
        Synopses $synopses,
        string $url,
        int $weighting,
        bool $isSuperPromotion,
        ?array $relatedLinks = null
    ) {
        $this->assertArrayOfType('related links', $relatedLinks, RelatedLink::class);

        $this->pid = $pid;
        $this->promotedEntity = $promotedEntity;
        $this->synopses = $synopses;
        $this->title = $title;
        $this->url = $url;
        $this->weighting = $weighting;
        $this->isSuperPromotion = $isSuperPromotion;
        $this->relatedLinks = $relatedLinks;
    }

    public function getPid(): Pid
    {
        return $this->pid;
    }

    public function getPromotedEntity(): PromotableInterface
    {
        return $this->promotedEntity;
    }

    public function getSynopses(): Synopses
    {
        return $this->synopses;
    }

    public function getShortSynopsis(): string
    {
        return $this->synopses->getShortSynopsis();
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getUrl(): string
    {
        return $this->url;
    }

    public function getWeighting(): int
    {
        return $this->weighting;
    }

    public function isSuperPromotion(): bool
    {
        return $this->isSuperPromotion;
    }

    /**
     * @return RelatedLink[]
     * @throws DataNotFetchedException
     */
    public function getRelatedLinks(): array
    {
        if (is_null($this->relatedLinks)) {
            throw new DataNotFetchedException('Could not get Related Links of Promotion "' . $this->pid . '" as they were not fetched');
        }

        return $this->relatedLinks;
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
                    'Tried to create a Promotion with invalid %s. Expected an array of %s but the array contained an instance of "%s"',
                    $property,
                    $expectedType,
                    (is_object($item) ? get_class($item) : gettype($item))
                ));
            }
        }
    }
}
