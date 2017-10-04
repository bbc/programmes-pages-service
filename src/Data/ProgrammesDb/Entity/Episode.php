<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\BroadcastCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\EntityInterfaces\StreamableClipCountableInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AggregatedBroadcastsCountMethodsTrait;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\AvailableClipsCountMethodsTrait;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Episode extends ProgrammeItem implements StreamableClipCountableInterface, BroadcastCountableInterface
{
    use AggregatedBroadcastsCountMethodsTrait;
    use AvailableClipsCountMethodsTrait;
}
