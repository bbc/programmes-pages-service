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
    private $isStreamable = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $hasSupportingContent = false;

    /**
     * @var string|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $position;


    public function getPromotionsCount(): int
    {
        return $this->promotionsCount;
    }

    public function setPromotionsCount(int $promotionsCount)
    {
        $this->promotionsCount = $promotionsCount;
    }

    public function getIsStreamable(): bool
    {
        return $this->isStreamable;
    }

    public function setIsStreamable(bool $isStreamable)
    {
        $this->isStreamable = $isStreamable;
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
}
