<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use InvalidArgumentException;

class BroadcastRepository extends EntityRepository
{
    use Traits\SetLimitTrait;
    use Traits\ParentTreeWalkerTrait;

    /**
     * @param array $dbIds
     * @param string $type
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     *
     * @return mixed
     */
    public function findByVersion(array $dbIds, string $type, $limit, int $offset)
    {
        $qb = $this->createQueryBuilder('broadcast')
            ->addSelect(['service', 'network'])
            // Left join as Webcasts are not attached to services
            ->leftJoin('broadcast.service', 'service')
            ->leftJoin('service.network', 'network')
            ->andWhere("broadcast.version IN (:dbIds)")
            ->addOrderBy('broadcast.startAt', 'DESC')
            ->addOrderBy('service.sid', 'DESC')
            ->setFirstResult($offset)
            ->setParameter('dbIds', $dbIds);

        $qb = $this->setLimit($qb, $limit);

        $this->setEntityTypeFilter($qb, $type);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findAllYearsAndMonthsByProgramme(array $ancestry, string $type)
    {
        $qb = $this->createQueryBuilder('broadcast', true)
            ->select(['DISTINCT YEAR(broadcast.startAt) as year', 'MONTH(broadcast.startAt) as month'])
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->andWhere('programmeItem.ancestry LIKE :ancestryClause')
            ->addOrderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC')
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');

        $qb = $this->setEntityTypeFilter($qb, $type);

        return $qb->getQuery()->getResult(Query::HYDRATE_SCALAR);
    }

    public function findByProgrammeAndMonth(array $ancestry, string $type, int $year, int $month)
    {
        $qb = $this->createQueryBuilder('broadcast', false)
                   ->addSelect(['programmeItem', 'masterBrand', 'network'])
                   ->addSelect(['GROUP_CONCAT(service.sid ORDER BY service.sid) as serviceIds'])
                   ->join('broadcast.service', 'service')
                   ->leftJoin('programmeItem.masterBrand', 'masterBrand')
                   ->leftJoin('masterBrand.network', 'network')
                   ->andWhere('programmeItem.ancestry LIKE :ancestryClause')
                   ->andWhere('YEAR(broadcast.startAt) = :year')
                   ->andWhere('MONTH(broadcast.startAt) = :month')
                   ->addGroupBy('broadcast.startAt')
                   ->addGroupBy('programmeItem.id')
                   ->addOrderBy('broadcast.startAt', 'DESC')
                   ->addOrderBy('service.urlKey', 'ASC')
                   ->setParameter('year', $year)
                   ->setParameter('month', $month)
                   ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');

        $qb = $this->setEntityTypeFilter($qb, $type);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $result = array_map(
            function ($res) {
                $rtn = $res[0];
                $rtn['serviceIds'] = explode(',', $res['serviceIds']);
                return $rtn;
            },
            $result
        );

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
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

    private function setEntityTypeFilter($qb, $type, $broadcastAlias = 'broadcast')
    {
        $typesLookup = [
            'Broadcast' => false,
            'Webcast' => true,
            'Any' => null,
        ];

        $typeNames = array_keys($typesLookup);

        if (!in_array($type, $typeNames)) {
            throw new InvalidArgumentException(sprintf(
                'Called %s with an invalid type. Expected one of %s but got "%s"',
                debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['function'],
                '"' . implode('", "', $typeNames) . '"',
                $type
            ));
        }

        $isWebcast = $typesLookup[$type] ?? null;

        if (!is_null($isWebcast)) {
            $qb->andWhere($broadcastAlias . '.isWebcast = :isWebcast')
               ->setParameter('isWebcast', $isWebcast);
        }

        return $qb;
    }

    private function ancestryIdsToString(array $ancestry)
    {
        return implode(',', $ancestry) . ',';
    }

    private function programmeAncestryGetter(array $ids)
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $repo->findByIds($ids);
    }
}
