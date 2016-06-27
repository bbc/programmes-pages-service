<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class GroupProgrammeContainer extends Group
{
    use Traits\AggregatedEpisodesCountMethodsTrait;
    use Traits\AvailableClipsCountMethodsTrait;
    use Traits\AvailableEpisodesCountMethodsTrait;
    use Traits\AvailableGalleriesCountMethodsTrait;
}
