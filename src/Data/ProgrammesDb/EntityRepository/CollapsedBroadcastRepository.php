<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;

class CollapsedBroadcastRepository extends EntityRepository
{
    public const NO_SERVICE = 'NULL';

    public function createQueryBuilder($alias, $indexBy = null)
    {
        return parent::createQueryBuilder($alias)
            ->join($alias . '.programmeItem', 'programmeItem');
    }
}
