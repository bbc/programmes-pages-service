<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
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

    public function findByCategoryAncestryAndEndAtDateRange(
        array $categoryAncestry,
        string $type,
        $medium,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        $limit,
        int $offset
    ) {
        $qb = $this->createCollapsedBroadcastsOfCategoryQueryBuilder($categoryAncestry, $type);

        $qb->andWhere('broadcast.endAt > :from')
            ->andWhere('broadcast.endAt <= :to')
            ->addOrderBy('broadcast.startAt')
            ->addOrderBy('networkOfService.urlKey')
            ->setFirstResult($offset)
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        $qb = $this->setLimit($qb, $limit);

        if ($this->isValidNetworkMedium($medium)) {
            $qb->andWhere('networkOfService.medium = :medium')
                ->setParameter('medium', $medium);
        }

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $result = $this->explodeServiceIds($result);

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
    }

    public function findByCategoryAncestryAndStartAtDateRange(
        array $categoryAncestry,
        string $type,
        $medium,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        $limit,
        int $offset
    ) {
        $qb = $this->createCollapsedBroadcastsOfCategoryQueryBuilder($categoryAncestry, $type);

        $qb->andWhere('broadcast.startAt >= :from')
           ->andWhere('broadcast.startAt < :to')
           ->addOrderBy('broadcast.startAt')
           ->addOrderBy('networkOfService.urlKey')
           ->setFirstResult($offset)
           ->setParameter('from', $from)
           ->setParameter('to', $to);

        $qb = $this->setLimit($qb, $limit);

        if ($this->isValidNetworkMedium($medium)) {
            $qb->andWhere('networkOfService.medium = :medium')
               ->setParameter('medium', $medium);
        }

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $result = $this->explodeServiceIds($result);

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
    }

    public function findBroadcastedDatesForCategories(
        array $categoryAncestries,
        string $type,
        $medium,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {
        $ancestryClause = [];
        foreach ($categoryAncestries as $categoryAncestry) {
            $ancestryClause[] = "category.ancestry LIKE '" . $this->ancestryIdsToString($categoryAncestry) . "%'";
        }

        $qb = $this->createQueryBuilder('broadcast', false)
            ->select('DISTINCT category.ancestry, DAY(broadcast.startAt) as day, MONTH(broadcast.startAt) as month, YEAR(broadcast.startAt) as year')
            ->innerJoin('programmeItem.categories', 'category')
            ->andWhere(implode(' OR ', $ancestryClause))
            ->andWhere('broadcast.startAt >= :from')
            ->andWhere('broadcast.startAt < :to')
            ->addOrderBy('broadcast.startAt')
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        $qb = $this->setEntityTypeFilter($qb, $type);

        if ($this->isValidNetworkMedium($medium)) {
            $qb->join('broadcast.service', 'service')
                ->innerJoin('service.network', 'networkOfService')
                ->andWhere('networkOfService.medium = :medium')
                ->setParameter('medium', $medium);
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_SCALAR);
    }

    public function countByCategoryAncestryAndEndAtDateRange(
        array $categoryAncestry,
        string $type,
        $medium,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): int {
        $isWebcastValue = $this->entityTypeFilterValue($type);
        $isWebcastClause = !is_null($isWebcastValue) ? 'AND b.is_webcast = :isWebcast' : '';

        $filterByMediumClause = $this->isValidNetworkMedium($medium) ? 'AND n.medium = :medium' : '';

        // Join to CoreEntity to ensure the programme is not embargoed
        // Join to network (via broadcast service) so that we get a count of
        // items grouped by network.
        //  For instance consider a programme broadcast on bbc_one_london and
        // bbc_one_yorkshire at the same time. This would result in a count of
        // one as those two services both belong to the same network - bbc_one.
        // However consider a programme broadcast on bbc_radio_ulster and
        // bbc_radio_foyle at the same time. This would result in a count of two
        // as these two services do not belong to the same network.
        $qText = <<<QUERY
SELECT COUNT(t.id) as cnt
FROM (
    SELECT b.start_at, c.id
    FROM broadcast b
    INNER JOIN core_entity c ON b.programme_item_id = c.id AND (c.is_embargoed = 0)
    INNER JOIN programme_category pc ON c.id = pc.programme_id
    INNER JOIN category cat ON pc.category_id = cat.id
    LEFT JOIN service s ON b.service_id = s.id
    LEFT JOIN network n ON s.network_id = n.id
    WHERE c.type = 'episode'
    AND cat.ancestry LIKE :categoryAncestry
    AND b.end_at > :cutoffTime
    AND b.end_at <= :limitTime
    $isWebcastClause
    $filterByMediumClause
    GROUP BY b.start_at, c.id, n.id
) t
QUERY;

        $rsm = new ResultSetMapping();
        $rsm->addScalarResult('cnt', 'cnt');

        $q = $this->getEntityManager()->createNativeQuery($qText, $rsm)
            ->setParameter('categoryAncestry', $this->ancestryIdsToString($categoryAncestry) . '%')
            ->setParameter('cutoffTime', $from)
            ->setParameter('limitTime', $to);

        if (!is_null($isWebcastValue)) {
            $q->setParameter('isWebcast', $isWebcastValue);
        }

        if ($this->isValidNetworkMedium($medium)) {
            $q->setParameter('medium', $medium);
        }

        return $q->getSingleScalarResult();
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
            ->addOrderBy('networkOfService.nid', 'ASC')
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
            ->addOrderBy('networkOfService.position', 'ASC')
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

        // Join to CoreEntity to ensure the programme is not embargoed
        // Join to network (via broadcast service) so that we get a count of
        // items grouped by network.
        //  For instance consider a programme broadcast on bbc_one_london and
        // bbc_one_yorkshire at the same time. This would result in a count of
        // one as those two services both belong to the same network - bbc_one.
        // However consider a programme broadcast on bbc_radio_ulster and
        // bbc_radio_foyle at the same time. This would result in a count of two
        // as these two services do not belong to the same network.

        $qText = <<<QUERY
SELECT COUNT(t.id) as cnt
FROM (
    SELECT b.start_at, c.id
    FROM broadcast b
    INNER JOIN core_entity c ON b.programme_item_id = c.id AND (c.is_embargoed = 0)
    LEFT JOIN service s ON b.service_id = s.id
    LEFT JOIN network n ON s.network_id = n.id
    WHERE c.type = 'episode'
    AND c.ancestry LIKE :ancestryClause
    AND b.end_at > :cutoffTime
    $isWebcastClause
    GROUP BY b.start_at, c.id, n.id
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
        // networkOfService is needed so that each row is contains the services
        // within a given network, rather than all services across multiple
        // networks.
        // For instance consider a programme broadcast on bbc_one_london and
        // bbc_one_yorkshire at the same time. This would result in one row
        // where the the servicesIds are "bbc_one_london,bbc_one_yorkshire" as
        // those two services both belong to the same network - bbc_one.
        // However consider a programme broadcast on bbc_radio_ulster and
        // bbc_radio_foyle at the same time. This would result in two rows as
        // these two services do not belong to the same network.
        $qb = $this->createQueryBuilder('broadcast', false)
            ->addSelect(['programmeItem', 'image', 'masterBrand', 'network'])
            ->addSelect(['GROUP_CONCAT(service.sid ORDER BY service.sid) as serviceIds'])
            ->join('broadcast.service', 'service')
            ->leftJoin('service.network', 'networkOfService')
            ->leftJoin('programmeItem.image', 'image')
            ->leftJoin('programmeItem.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->andWhere('programmeItem.ancestry LIKE :ancestryClause')
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->addGroupBy('broadcast.startAt')
            ->addGroupBy('programmeItem.id')
            ->addGroupBy('networkOfService.id')
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');

        $qb = $this->setEntityTypeFilter($qb, $type);
        return $qb;
    }

    private function createCollapsedBroadcastsOfCategoryQueryBuilder(array $ancestry, string $type)
    {
        // networkOfService is needed so that each row contains the services
        // within a given network, rather than all services across multiple networks.
        // For instance consider a programme broadcast on bbc_one_london and
        // bbc_one_yorkshire at the same time. This would result in one row
        // where the the servicesIds are "bbc_one_london,bbc_one_yorkshire" as
        // those two services both belong to the same network - bbc_one.
        // However consider a programme broadcast on bbc_radio_ulster and
        // bbc_radio_foyle at the same time. This would result in two rows as
        // these two services do not belong to the same network.
        $qb = $this->createQueryBuilder('broadcast', false)
            ->addSelect(['category', 'programmeItem', 'image'])
            ->addSelect(['GROUP_CONCAT(service.sid ORDER BY service.sid) as serviceIds'])
            ->join('broadcast.service', 'service')
            ->leftJoin('service.network', 'networkOfService')
            ->leftJoin('programmeItem.image', 'image')
            ->innerJoin('programmeItem.categories', 'category')
            ->andWhere('category.ancestry LIKE :ancestryClause')
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->addGroupBy('broadcast.startAt')
            ->addGroupBy('programmeItem.id')
            ->addGroupBy('networkOfService.id')
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

    private function isValidNetworkMedium($medium)
    {
        return in_array($medium, [NetworkMediumEnum::TV, NetworkMediumEnum::RADIO]);
    }
}
