<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

trait BroadcastTrait
{
    private function ancestryIdsToString(array $ancestry): string
    {
        return implode(',', $ancestry) . ',';
    }

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

    private function programmeAncestryGetter(array $ids): array
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $repo->findByIds($ids);
    }

    private function explodeField(array $collapsedBroadcasts, string $field): array
    {
        return array_map(
            function ($collapsedBroadcast) use ($field) {
                // The last character is always a comma, which makes explode return an extra empty element
                // as the last one. Leaving it could cause problems, so the -1 down here \/ removes it.
                $collapsedBroadcast[$field] = explode(',', $collapsedBroadcast[$field], -1);
                return $collapsedBroadcast;
            },
            $collapsedBroadcasts
        );
    }
}
