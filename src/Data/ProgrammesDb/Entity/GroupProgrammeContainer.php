<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\GalleriesCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableClipCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableEpisodeCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedEpisodesCountMethodsTrait;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedGalleriesCountMethodsTrait;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableClipsCountMethodsTrait;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableEpisodesCountMethodsTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class GroupProgrammeContainer extends Group implements StreamableClipCountableInterface, StreamableEpisodeCountableInterface, GalleriesCountableInterface
{
    use AggregatedEpisodesCountMethodsTrait;
    use AvailableClipsCountMethodsTrait;
    use AvailableEpisodesCountMethodsTrait;
    use AggregatedGalleriesCountMethodsTrait;
}
