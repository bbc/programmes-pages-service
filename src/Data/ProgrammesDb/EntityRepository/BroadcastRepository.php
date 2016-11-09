<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use DateTimeImmutable;
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

    public function findByProgrammeAndMonth(array $ancestry, string $type, int $year, int $month, $limit, int $offset)
    {
        $qb = $this->createCollapsedBroadcastsOfProgrammeQueryBuilder(
            $ancestry,
            $type
        );

        $qb->andWhere('YEAR(broadcast.startAt) = :year')
            ->andWhere('MONTH(broadcast.startAt) = :month')
            ->addOrderBy('broadcast.startAt', 'DESC')
            ->addOrderBy('service.urlKey', 'ASC')
            ->setFirstResult($offset)
            ->setParameter('year', $year)
            ->setParameter('month', $month);

        $qb = $this->setLimit($qb, $limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $result = $this->explodeServiceIds($result);

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
    }

    /**
     * Past Broadcasts are different from Broadcasts within a date range.
     * Within a date range we look for programmes that /started/ no later than
     * the end date parameter.
     * However for past programmes we don't want to count broadcasts that are in
     * progress at the cutoffTime, so thus we need to look for programmes that
     * /ended/ earlier than the cutoffTime parameter.
     */
    public function findPastByProgramme(
        array $ancestry,
        string $type,
        DateTimeImmutable $cutoffTime,
        $limit,
        int $offset
    ) {
        $qb = $this->createCollapsedBroadcastsOfProgrammeQueryBuilder(
            $ancestry,
            $type
        );

        $qb->andWhere('broadcast.endAt <= :endTime')
            ->addOrderBy('broadcast.endAt', 'DESC')
            ->addOrderBy('network.nid')
            ->setFirstResult($offset)
            ->setParameter('endTime', $cutoffTime);

        $qb = $this->setLimit($qb, $limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $result = $this->explodeServiceIds($result);

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
    }

    /**
     * Upcoming Broadcasts are different from Broadcasts within a date range.
     * Within a date range we look for programmes that /started/ no earlier than
     * the start date parameter.
     * However for upcoming programmes we want to include programmes that are
     * on-air at the cutoffTime, so thus we need to look for programmes that
     * /ended/ after the cutoffTime parameter.
     */
    public function findUpcomingByProgramme(
        array $ancestry,
        string $type,
        DateTimeImmutable $cutoffTime,
        $limit,
        int $offset
    ) {
        $qb = $this->createCollapsedBroadcastsOfProgrammeQueryBuilder(
            $ancestry,
            $type
        );

        $qb->andWhere('broadcast.endAt > :cutoffTime')
            ->addOrderBy('broadcast.startAt', 'ASC')
            // Secondary sort in APS differs between upcoming and past by
            // programme. We should standardise this at some point.
            ->addOrderBy('network.position')
            ->setFirstResult($offset)
            ->setParameter('cutoffTime', $cutoffTime);

        $qb = $this->setLimit($qb, $limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $result = $this->explodeServiceIds($result);

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
    }

    public function countUpcomingByProgramme(
        array $ancestry,
        string $type,
        DateTimeImmutable $cutoffTime
    ): int {
        $isWebcastValue = $this->entityTypeFilterValue($type);
        $isWebcastClause = !is_null($isWebcastValue) ? 'AND b.is_webcast = :isWebcast' : '';

        $qText = <<<QUERY
SELECT COUNT(t.id) as cnt
FROM (
    SELECT b.start_at, c.id
    FROM broadcast b
    INNER JOIN core_entity c ON b.programme_item_id = c.id AND (c.is_embargoed = 0)
    WHERE c.type = 'episode'
    AND c.ancestry LIKE :ancestryClause
    AND b.end_at > :cutoffTime
    $isWebcastClause
    GROUP BY b.start_at, c.id
) t
QUERY;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('cnt', 'cnt');

        $q = $this->getEntityManager()->createNativeQuery($qText, $rsm)
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%')
            ->setParameter('cutoffTime', $cutoffTime);

        if (!is_null($isWebcastValue)) {
            $q->setParameter('isWebcast', $isWebcastValue);
        }

        return $q->getSingleScalarResult();
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

    private function createCollapsedBroadcastsOfProgrammeQueryBuilder($ancestry, $type)
    {
        $qb = $this->createQueryBuilder('broadcast', false)
            ->addSelect(['programmeItem', 'masterBrand', 'network'])
            ->addSelect(['GROUP_CONCAT(service.sid ORDER BY service.sid) as serviceIds'])
            ->join('broadcast.service', 'service')
            ->leftJoin('programmeItem.image', 'image')
            ->leftJoin('programmeItem.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->andWhere('programmeItem.ancestry LIKE :ancestryClause')
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->addGroupBy('broadcast.startAt')
            ->addGroupBy('programmeItem.id')
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');

        $qb = $this->setEntityTypeFilter($qb, $type);
        return $qb;
    }

    private function entityTypeFilterValue($type)
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

        return $typesLookup[$type] ?? null;
    }


    private function setEntityTypeFilter($qb, $type, $broadcastAlias = 'broadcast')
    {
        $isWebcast = $this->entityTypeFilterValue($type);

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

    private function explodeServiceIds($collapsedBroadcasts)
    {
        return array_map(
            function ($collapsedBroadcast) {
                $rtn = $collapsedBroadcast[0];
                $rtn['serviceIds'] = explode(',', $collapsedBroadcast['serviceIds']);

                return $rtn;
            },
            $collapsedBroadcasts
        );
    }

    private function programmeAncestryGetter(array $ids)
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $repo->findByIds($ids);
    }
}
