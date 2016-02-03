<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Episode extends ProgrammeItem
{
    use Traits\AggregatedBroadcastsCountTrait;
    use Traits\AvailableClipsCountTrait;
    use Traits\AvailableGalleriesCountTrait;
}
