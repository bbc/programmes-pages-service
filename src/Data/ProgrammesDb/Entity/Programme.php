<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use DateTime;
use Doctrine\Common\Collections\Collection as DoctrineCollection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class Programme extends CoreEntity
{
    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $hasSupportingContent = false;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $position;

    /**
     * @var DateTime
     *
     * @ORM\Column(type="datetime", nullable=true)
     */
    private $firstBroadcastDate;

    public function getPromotionsCount(): int
    {
        return $this->promotionsCount;
    }

    public function setPromotionsCount(int $promotionsCount): void
    {
        $this->promotionsCount = $promotionsCount;
    }

    public function getStreamable(): bool
    {
        return $this->streamable;
    }

    public function setStreamable(bool $streamable): void
    {
        $this->streamable = $streamable;
    }

    public function getStreamableAlternate(): bool
    {
        return $this->streamableAlternate;
    }

    public function setStreamableAlternate(bool $streamableAlternate): void
    {
        $this->streamableAlternate = $streamableAlternate;
    }

    public function getHasSupportingContent(): bool
    {
        return $this->hasSupportingContent;
    }

    public function setHasSupportingContent(bool $hasSupportingContent): void
    {
        $this->hasSupportingContent = $hasSupportingContent;
    }

    public function getPosition(): ?int
    {
        return $this->position;
    }

    public function setPosition(?int $position): void
    {
        $this->position = $position;
    }

    /**
     * The property is defined on the CoreEntity class, because doctrine throws
     * all of its toys out of the pram if I define it here, it can only
     * be get/set here though.
     * This is the list of categories directly associated with a programme.
     */
    public function getDirectCategories(): DoctrineCollection
    {
        return $this->directCategories;
    }

    public function setDirectCategories(DoctrineCollection $directCategories): void
    {
        $this->directCategories = $directCategories;
    }

    /**
     * The property is defined on the CoreEntity class, because doctrine throws
     * all of its toys out of the pram if I define it here, it can only
     * be get/set here though.
     * This is the denormalized list of categories associated with a programme,
     * or any any of its ancestors
     *
     */
    public function getCategories(): DoctrineCollection
    {
        return $this->categories;
    }

    public function setCategories(DoctrineCollection $categories): void
    {
        $this->categories = $categories;
    }

    public function getFirstBroadcastDate(): ?DateTime
    {
        return $this->firstBroadcastDate;
    }

    public function setFirstBroadcastDate(?DateTime $firstBroadcastDate): void
    {
        $this->firstBroadcastDate = $firstBroadcastDate;
    }
}
