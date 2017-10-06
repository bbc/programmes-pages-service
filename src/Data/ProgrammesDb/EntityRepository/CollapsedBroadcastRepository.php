<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\ResultSetMapping;
use Doctrine\ORM\QueryBuilder;

class CollapsedBroadcastRepository extends EntityRepository
{
    public const NO_SERVICE = 'NULL';

    use Traits\ParentTreeWalkerTrait;

    public function countByCategoryAncestryAndEndAtDateRange(
        array $categoryAncestry,
        bool $isWebcastOnly,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): int {

        // A programme could have two categories from the same ancestry tree assigned to it.
        // So, a collapsed broadcast would be returned once for each category from that ancestry.
        // We need to group by the programme id and start time to avoid getting duplicate broadcasts.
        $qText = <<<QUERY
SELECT COUNT(t.id) as cnt
FROM (
    SELECT cb.id
    FROM collapsed_broadcast cb
    INNER JOIN core_entity c ON c.id = cb.programme_item_id AND (c.is_embargoed = 0)
    INNER JOIN programme_category pc ON c.id = pc.programme_id
    INNER JOIN category cat ON pc.category_id = cat.id
    WHERE cat.ancestry LIKE :categoryAncestry
    AND cb.end_at > :from
    AND cb.end_at <= :to
    AND cb.is_webcast_only = :isWebcastOnly
    GROUP BY cb.start_at, c.id
) t
QUERY;

        $rms = new ResultSetMapping();
        $rms->addScalarResult('cnt', 'cnt');

        $q = $this->getEntityManager()->createNativeQuery($qText, $rms)
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('categoryAncestry', $this->ancestryIdsToString($categoryAncestry) . '%');

        return $q->getSingleScalarResult();
    }

    public function countUpcomingByProgramme(
        array $ancestry,
        bool $isWebcastOnly,
        DateTimeImmutable $from
    ): int {
        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->select('COUNT(collapsedBroadcast)')
            ->andWhere('collapsedBroadcast.endAt > :from')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('from', $from);

        if (count($ancestry) === 1) {
            $qb->andWhere('IDENTITY(collapsedBroadcast.tleo) = :tleoId')
                ->setParameter('tleoId', $ancestry[0]);
        } else {
            $qb->andWhere('programmeItem.ancestry LIKE :ancestryClause')
                ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');
        }

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findByCategoryAncestryAndEndAtDateRange(
        array $categoryAncestry,
        bool $isWebcastOnly,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?int $limit,
        int $offset
    ): array {
        $qb = $this->createCollapsedBroadcastsOfCategoryQueryBuilder($categoryAncestry, $isWebcastOnly)
            ->andWhere('collapsedBroadcast.endAt > :from')
            ->andWhere('collapsedBroadcast.endAt <= :to')
            ->addOrderBy('collapsedBroadcast.startAt')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $result = $this->explodeFields($result);

        return $this->resolveProgrammeParents($result);
    }

    public function findByCategoryAncestryAndStartAtDateRange(
        array $categoryAncestry,
        bool $isWebcastOnly,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?int $limit,
        int $offset
    ): array {
        $qb = $this->createCollapsedBroadcastsOfCategoryQueryBuilder($categoryAncestry, $isWebcastOnly)
            ->andWhere('collapsedBroadcast.startAt >= :from')
            ->andWhere('collapsedBroadcast.startAt < :to')
            ->addOrderBy('collapsedBroadcast.startAt')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $result = $this->explodeFields($result);

        return $this->resolveProgrammeParents($result);
    }

    public function findByProgrammeAndMonth(
        array $ancestry,
        bool $isWebcastOnly,
        int $year,
        int $month,
        ?int $limit,
        int $offset
    ): array {
        $qb = $this->createCollapsedBroadcastsOfProgrammeQueryBuilder($ancestry, $isWebcastOnly)
            ->andWhere('YEAR(collapsedBroadcast.startAt) = :year')
            ->andWhere('MONTH(collapsedBroadcast.startAt) = :month')
            ->addOrderBy('collapsedBroadcast.startAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('year', $year)
            ->setParameter('month', $month);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $result = $this->explodeFields($result);

        return $this->resolveProgrammeParents($result);
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
        bool $isWebcastOnly,
        DateTimeImmutable $cutoffTime,
        ?int $limit,
        int $offset
    ): array {
        $qb = $this->createCollapsedBroadcastsOfProgrammeQueryBuilder($ancestry, $isWebcastOnly)
            ->andWhere('collapsedBroadcast.endAt <= :endTime')
            ->addOrderBy('collapsedBroadcast.endAt', 'DESC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('endTime', $cutoffTime);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $result = $this->explodeFields($result);

        return $this->resolveProgrammeParents($result);
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
        bool $isWebcastOnly,
        DateTimeImmutable $cutoffTime,
        ?int $limit,
        int $offset
    ): array {
        $qb = $this->createCollapsedBroadcastsOfProgrammeQueryBuilder($ancestry, $isWebcastOnly)
            ->andWhere('collapsedBroadcast.endAt > :cutoffTime')
            ->addOrderBy('collapsedBroadcast.startAt', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('cutoffTime', $cutoffTime);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        $result = $this->explodeFields($result);

        return $this->resolveProgrammeParents($result);
    }

    public function filterCategoriesByBroadcastedDates(
        array $categoryAncestries,
        bool $isWebcastOnly,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {

        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->select('DISTINCT category.ancestry')
            ->innerJoin('programmeItem.categories', 'category')
            ->andWhere('collapsedBroadcast.startAt >= :from')
            ->andWhere('collapsedBroadcast.startAt < :to')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->addOrderBy('collapsedBroadcast.startAt')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('isWebcastOnly', $isWebcastOnly);

        $orExpressions = [];
        $i = 0;
        foreach ($categoryAncestries as $categoryAncestry) {
            $paramName = 'ancestry' . $i;
            $orExpressions[] = $qb->expr()->like('category.ancestry', ':' . $paramName);
            $qb->setParameter($paramName, $this->ancestryIdsToString($categoryAncestry) . '%');
            $i++;
        }
        $qb->andWhere($qb->expr()->orX(
            ...$orExpressions
        ));
        return $qb->getQuery()->getResult(Query::HYDRATE_SCALAR);
    }

    public function findBroadcastedDatesForCategory(
        array $categoryAncestry,
        bool $isWebcastOnly,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {
        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->select('DISTINCT DAY(collapsedBroadcast.startAt) as day, MONTH(collapsedBroadcast.startAt) as month, YEAR(collapsedBroadcast.startAt) as year')
            ->innerJoin('programmeItem.categories', 'category')
            ->andWhere('category.ancestry LIKE :ancestry')
            ->andWhere('collapsedBroadcast.startAt >= :from')
            ->andWhere('collapsedBroadcast.startAt < :to')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->addOrderBy('collapsedBroadcast.startAt')
            ->setParameter('ancestry', $this->ancestryIdsToString($categoryAncestry) . '%')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('isWebcastOnly', $isWebcastOnly);

        return $qb->getQuery()->getResult(Query::HYDRATE_SCALAR);
    }

    public function findAllYearsAndMonthsByProgramme(array $ancestry, bool $isWebcastOnly): array
    {
        $qb = $this->createQueryBuilder('collapsedBroadcast', true)
            ->select(['DISTINCT YEAR(collapsedBroadcast.startAt) as year', 'MONTH(collapsedBroadcast.startAt) as month'])
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->andWhere('programmeItem.ancestry LIKE :ancestryClause')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->addOrderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC')
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%')
            ->setParameter('isWebcastOnly', $isWebcastOnly);

        return $qb->getQuery()->getResult(Query::HYDRATE_SCALAR);
    }

    public function findByStartAndProgrammeItemId(DateTimeImmutable $start, int $id, bool $isWebcastOnly = false)
    {
        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->addSelect(['programmeItem', 'image', 'masterBrand', 'mbNetwork'])
            ->leftJoin('programmeItem.image', 'image')
            ->leftJoin('programmeItem.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'mbNetwork')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->andWhere('collapsedBroadcast.startAt = :start')
            ->andWhere('IDENTITY(collapsedBroadcast.programmeItem) = :id')
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('start', $start)
            ->setParameter('id', $id);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->explodeFields($result);
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        return parent::createQueryBuilder($alias)
            ->join($alias . '.programmeItem', 'programmeItem');
    }

    private function createCollapsedBroadcastsOfProgrammeQueryBuilder(array $ancestry, bool $isWebcastOnly): QueryBuilder
    {
        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->addSelect(['programmeItem', 'image', 'masterBrand', 'mbNetwork'])
            ->leftJoin('programmeItem.image', 'image')
            ->leftJoin('programmeItem.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'mbNetwork')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->setParameter('isWebcastOnly', $isWebcastOnly);

        if (count($ancestry) === 1) {
            $qb->andWhere('IDENTITY(collapsedBroadcast.tleo) = :tleoid')
                ->setParameter('tleoid', $ancestry[0]);
        } else {
            $qb->andWhere('programmeItem.ancestry LIKE :ancestryClause')
                ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');
        }

        return $qb;
    }

    private function createCollapsedBroadcastsOfCategoryQueryBuilder(array $ancestry, bool $isWebcastOnly): QueryBuilder
    {
        return $this->createQueryBuilder('collapsedBroadcast', false)
            ->addSelect(['category', 'programmeItem', 'image', 'masterBrand', 'mbNetwork'])
            ->leftJoin('programmeItem.image', 'image')
            ->innerJoin('programmeItem.categories', 'category')
            ->leftJoin('programmeItem.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'mbNetwork')
            ->andWhere('category.ancestry LIKE :ancestryClause')
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->addGroupBy('programmeItem.id')
            ->addGroupBy('collapsedBroadcast.startAt')
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');
    }

    private function explodeFields(array $result): array
    {
        return array_map(
            function ($collapsedBroadcast) {
                $collapsedBroadcast['serviceIds'] = explode(',', $collapsedBroadcast['serviceIds']);
                $collapsedBroadcast['broadcastIds'] = explode(',', $collapsedBroadcast['broadcastIds']);
                $collapsedBroadcast['areWebcasts'] = explode(',', $collapsedBroadcast['areWebcasts']);
                return $collapsedBroadcast;
            },
            $result
        );
    }

    private function ancestryIdsToString(array $ancestry): string
    {
        return implode(',', $ancestry) . ',';
    }

    private function resolveProgrammeParents(array $result)
    {
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $this->abstractResolveAncestry(
            $result,
            [$repo, 'coreEntityAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
    }
}
