<?php

namespace BBC\ProgrammesPagesService\Domain\Entity;

class Podcast
{
    /** @var int */
    private $availability;

    /** @var ProgrammeItem|Collection */
    private $coreEntity;

    /** @var string */
    private $frequency;

    /** @var bool */
    private $isLowBitrate;

    /** @var bool */
    private $isUkOnly;

    /**
     * Podcast constructor.
     * @param ProgrammeItem|Collection $coreEntity
     * @param string $frequency
     * @param int $availability
     * @param bool $isUkOnly
     * @param bool $isLowBitrate
     */
    public function __construct(CoreEntity $coreEntity, string $frequency, int $availability, bool $isUkOnly, bool $isLowBitrate)
    {
        $this->coreEntity = $coreEntity;
        $this->frequency = $frequency;
        $this->availability = $availability;
        $this->isUkOnly = $isUkOnly;
        $this->isLowBitrate = $isLowBitrate;
    }

    public function getAvailability(): int
    {
        return $this->availability;
    }

    /**
     * @return ProgrammeItem|Collection
     */
    public function getCoreEntity(): CoreEntity
    {
        return $this->coreEntity;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function getIsLowBitrate(): bool
    {
        return $this->isLowBitrate;
    }

    public function getIsUkOnly(): bool
    {
        return $this->isUkOnly;
    }
}
