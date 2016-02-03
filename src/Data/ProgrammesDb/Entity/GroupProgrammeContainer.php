<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\MappedSuperclass()
 */
abstract class GroupProgrammeContainer extends Group
{
    use Traits\AggregatedEpisodesCountTrait;
    use Traits\AvailableClipsCountTrait;
    use Traits\AvailableEpisodesCountTrait;
    use Traits\AvailableGalleriesCountTrait;
}
