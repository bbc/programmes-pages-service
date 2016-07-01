<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\GalleriesCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableClipCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableEpisodeCountableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class GroupProgrammeContainer extends Group implements StreamableClipCountableInterface, StreamableEpisodeCountableInterface, GalleriesCountableInterface
{
    use Traits\AggregatedEpisodesCountMethodsTrait;
    use Traits\AvailableClipsCountMethodsTrait;
    use Traits\AvailableEpisodesCountMethodsTrait;
    use Traits\AvailableGalleriesCountMethodsTrait;
}
