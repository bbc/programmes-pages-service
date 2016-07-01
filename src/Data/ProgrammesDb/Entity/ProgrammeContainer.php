<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\BroadcastCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\EpisodeCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\GalleriesCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableClipCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableEpisodeCountableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class ProgrammeContainer extends Programme implements
    BroadcastCountableInterface,
    StreamableEpisodeCountableInterface,
    EpisodeCountableInterface,
    StreamableClipCountableInterface,
    GalleriesCountableInterface
{
    use Traits\AggregatedBroadcastsCountMethodsTrait;
    use Traits\AggregatedEpisodesCountMethodsTrait;
    use Traits\AvailableClipsCountMethodsTrait;
    use Traits\AvailableEpisodesCountMethodsTrait;
    use Traits\AvailableGalleriesCountMethodsTrait;
    use Traits\IsPodcastableMethodsTrait;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $expectedChildCount;

    /**
     * @return int|null
     */
    public function getExpectedChildCount()
    {
        return $this->expectedChildCount;
    }

    public function setExpectedChildCount(int $expectedChildCount = null)
    {
        $this->expectedChildCount = $expectedChildCount;
    }
}
