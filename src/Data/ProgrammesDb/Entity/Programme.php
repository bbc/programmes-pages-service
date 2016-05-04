<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class Programme extends CoreEntity
{
    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $promotionsCount = 0;

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
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $streamable = false;


    public function getPromotionsCount(): int
    {
        return $this->promotionsCount;
    }

    public function setPromotionsCount(int $promotionsCount)
    {
        $this->promotionsCount = $promotionsCount;
    }

    /**
     * @return boolean
     */
    public function getStreamable()
    {
        return $this->streamable;
    }

    public function setStreamable(bool $streamable)
    {
        $this->streamable = $streamable;
    }

    public function getHasSupportingContent(): bool
    {
        return $this->hasSupportingContent;
    }

    public function setHasSupportingContent(bool $hasSupportingContent)
    {
        $this->hasSupportingContent = $hasSupportingContent;
    }

    /**
     * @return int|null
     */
    public function getPosition()
    {
        return $this->position;
    }

    public function setPosition(int $position = null)
    {
        $this->position = $position;
    }

    /**
     * The property is defined on the CoreEntity class, because doctrine throws
     * all of its toys out of the pram if I define it here, it can only
     * be get/set here though.
     * This is the list of categories directly associated with a programme.
     *
     * @return Category[]|null
     */
    public function getDirectCategories()
    {
        return $this->directCategories;
    }

    /**
     * @param Category[] $directCategories
     */
    public function setDirectCategories(array $directCategories)
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
     * @return Category[]|null
     */
    public function getCategories()
    {
        return $this->categories;
    }

    /**
     * @param Category[] $categories
     */
    public function setCategories(array $categories)
    {
        $this->categories = $categories;
    }
}
