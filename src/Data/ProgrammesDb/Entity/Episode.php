<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\BroadcastCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\GalleriesCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableClipCountableInterface;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Episode extends ProgrammeItem implements StreamableClipCountableInterface, BroadcastCountableInterface, GalleriesCountableInterface
{
    use Traits\AggregatedBroadcastsCountMethodsTrait;
    use Traits\AvailableClipsCountMethodsTrait;
    use Traits\AvailableGalleriesCountMethodsTrait;
}
