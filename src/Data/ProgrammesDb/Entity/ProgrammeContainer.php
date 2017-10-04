<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\BroadcastCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\EpisodeCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableClipCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableEpisodeCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountMethodsTrait;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedEpisodesCountMethodsTrait;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableClipsCountMethodsTrait;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableEpisodesCountMethodsTrait;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\IsPodcastableMethodsTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class ProgrammeContainer extends Programme implements
    BroadcastCountableInterface,
    StreamableEpisodeCountableInterface,
    EpisodeCountableInterface,
    StreamableClipCountableInterface
{
    use AggregatedBroadcastsCountMethodsTrait;
    use AggregatedEpisodesCountMethodsTrait;
    use AvailableClipsCountMethodsTrait;
    use AvailableEpisodesCountMethodsTrait;
    use IsPodcastableMethodsTrait;

    /**
     * @var int|null
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    private $expectedChildCount;

    public function getExpectedChildCount(): ?int
    {
        return $this->expectedChildCount;
    }

    public function setExpectedChildCount(?int $expectedChildCount): void
    {
        $this->expectedChildCount = $expectedChildCount;
    }
}
