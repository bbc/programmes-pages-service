<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;
use Gedmo\Timestampable\Traits\TimestampableEntity;

/**
 * @ORM\Entity(repositoryClass="BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PodcastRepository")
 */
class Podcast
{
    use TimestampableEntity;

    /**
     * @var int
     *
     * @ORM\Id()
     * @ORM\Column(type="integer", nullable=false)
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var CoreEntity
     *
     * @ORM\ManyToOne(targetEntity="CoreEntity")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    private $coreEntity;

    /**
     * @var string
     *
     * @ORM\Column(type="string", length=12, nullable=false)
     */
    private $frequency;

    /**
     * @var int
     *
     * @ORM\Column(type="integer", nullable=false)
     */
    private $availability;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isUkOnly = false;

    /**
     * @var bool
     *
     * @ORM\Column(type="boolean", nullable=false)
     */
    private $isLowBitrate = false;

    public function __construct(CoreEntity $coreEntity, string $frequency, int $availability, bool $isUkOnly, bool $isLowBitrate)
    {
        $this->coreEntity = $coreEntity;
        $this->frequency = $frequency;
        $this->availability = $availability;
        $this->isUkOnly = $isUkOnly;
        $this->isLowBitrate = $isLowBitrate;
    }

    public function getId(): int
    {
        return $this->id;
    }

    public function getCoreEntity(): CoreEntity
    {
        return $this->coreEntity;
    }

    public function setCoreEntity(CoreEntity $coreEntity): void
    {
        $this->coreEntity = $coreEntity;
    }

    public function getFrequency(): string
    {
        return $this->frequency;
    }

    public function setFrequency(string $frequency): void
    {
        // TODO Validate frequency

        $this->frequency = $frequency;
    }

    public function getAvailability(): int
    {
        return $this->availability;
    }

    public function setAvailability(int $availability): void
    {
        // TODO Validate availability

        $this->availability = $availability;
    }

    public function getIsUkOnly(): bool
    {
        return $this->isUkOnly;
    }

    public function setIsUkOnly(bool $isUkOnly): void
    {
        $this->isUkOnly = $isUkOnly;
    }

    public function getIsLowBitrate(): bool
    {
        return $this->isLowBitrate;
    }

    public function setIsLowBitrate(bool $isLowBitrate): void
    {
        $this->isLowBitrate = $isLowBitrate;
    }
}
