<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity()
 */
class Collection extends GroupProgrammeContainer
{
    use Traits\IsPodcastableTrait;
}
