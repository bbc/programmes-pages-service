<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use BBC\ProgrammesPagesService\Domain\Enumeration\IsPodcastableEnum;
use Doctrine\ORM\Mapping as ORM;
use InvalidArgumentException;

trait IsPodcastableTrait
{
    /**
     * @var string
     *
     * @ORM\Column(type="string", nullable=false)
     */
    protected $isPodcastable = IsPodcastableEnum::NO;

    public function getIsPodcastable(): string
    {
        return $this->isPodcastable;
    }

    public function setIsPodcastable(string $isPodcastable)
    {
        if (!in_array($isPodcastable, [IsPodcastableEnum::HIGH, IsPodcastableEnum::LOW, IsPodcastableEnum::NO])) {
            throw new InvalidArgumentException(sprintf(
                'Called setIsPodcastable with an invalid value. Expected one of "%s", "%s" or "%s" but got "%s"',
                IsPodcastableEnum::HIGH,
                IsPodcastableEnum::LOW,
                IsPodcastableEnum::NO,
                $isPodcastable
            ));
        }

        $this->isPodcastable = $isPodcastable;
    }
}
