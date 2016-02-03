<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Franchise extends GroupProgrammeContainer
{
    use Traits\AggregatedBroadcastsCountTrait;
}
