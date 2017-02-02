<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

class CollapsedBroadcastRepository extends EntityRepository
{
    public function createQueryBuilder($alias, $joinViaVersion = true, $indexBy = null)
    {
        // Any time Broadcasts are fetched here they must be inner joined to
        // their programme entity - either directly - or via the version, this
        // allows the embargoed filter to trigger and exclude unwanted items.
        // This ensures that Broadcasts that belong to a version that belongs
        // to an embargoed programme are never returned
        if ($joinViaVersion) {
            return parent::createQueryBuilder($alias)
                ->join($alias . '.version', 'version')
                ->join('version.programmeItem', 'programmeItem');
        }

        return parent::createQueryBuilder($alias)
            ->join($alias . '.programmeItem', 'programmeItem');
    }
}
