<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class ProgrammeContainer extends Programme
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
