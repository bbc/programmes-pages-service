<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use Doctrine\ORM\Query;

class BroadcastRepository extends EntityRepository
{
    public function findByVersion(array $dbIds, string $type, int $limit, int $offset)
    {
        if (!in_array($type, ['Broadcast', 'Webcast', 'Any'])) {
            throw new InvalidArgumentException(sprintf(
                'Called findByVersion with an invalid type. Expected one of "%s", "%s" or "%s" but got "%s"',
                'Broadcast',
                'Webcast',
                'Any',
                $type
            ));
        }

        $typeLookup = [
            'Broadcast' => false,
            'Webcast' => true,
            'Any' => null,
        ];
        $isWebcast = $typeLookup[$type] ?? null;

        $qb = $this->createQueryBuilder('broadcast')
            ->addSelect(['service'])
            ->join('broadcast.service', 'service')
            ->addWhere("broadcast.version IN (:dbIds)")
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('dbIds', $dbIds);

        if (!is_null($isWebcast)) {
            $qb->addWhere('broadcast.isWebcast = :isWebcast')
                ->setParameter('isWebcast', $isWebcast);
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
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
}
