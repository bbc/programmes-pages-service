<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Image;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Network;
use DateTimeImmutable;
use Doctrine\ORM\AbstractQuery;
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

    /**
     * Count the number of upcoming debuts and repeats. This query will return an associative array with the keys
     * 'debuts' and 'repeats' containing the pertraining count
     */
    public function countUpcomingRepeatsAndDebutsByProgramme(
        array $ancestry,
        bool $isWebcastOnly,
        DateTimeImmutable $from
    ): array {
        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->select('collapsedBroadcast.isRepeat')
            ->addSelect('COUNT(collapsedBroadcast)')
            ->andWhere('collapsedBroadcast.endAt > :from')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->groupBy('collapsedBroadcast.isRepeat')
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('from', $from);

        if (count($ancestry) === 1) {
            $qb->andWhere('IDENTITY(collapsedBroadcast.tleo) = :tleoId')
                ->setParameter('tleoId', $ancestry[0]);
        } else {
            $qb->andWhere('programmeItem.ancestry LIKE :ancestryClause')
                ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');
        }

        $result = $qb->getQuery()->getArrayResult();

        $debuts = 0;
        $repeats = 0;

        if ($result) {
            foreach ($result as $r) {
                if ($r['isRepeat']) {
                    $repeats = (int) $r['1'];
                } else {
                    $debuts = (int) $r['1'];
                }
            }
        }

        return ['debuts' => $debuts, 'repeats' => $repeats];
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

    public function findByProgramme(
        int $programmeId,
        bool $isWebcastOnly,
        ?int $limit,
        int $offset
    ): array {
        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->andWhere('programmeItem.id = :programmeItemId')
            ->setParameter('programmeItemId', $programmeId)
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->addOrderBy('collapsedBroadcast.startAt', 'ASC')
            ->setFirstResult($offset)
            ->setMaxResults($limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->explodeFields($result);
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
     * When getting the next broadcast on, we prefer live now over debuts over repeats, hence the order by isRepeat
     * (debuts are 0, repeats are 1) and order by isLive
     */
    public function findNextDebutOrRepeatOnByProgramme(
        array $ancestry,
        bool $isWebcastOnly,
        DateTimeImmutable $cutoffTime
    ): array {
        $qb = $this->createCollapsedBroadcastsOfProgrammeQueryBuilder($ancestry, $isWebcastOnly)
            ->addSelect('CASE WHEN collapsedBroadcast.startAt <= :cutoffTime THEN 1 ELSE 0 END AS HIDDEN isLive')
            ->andWhere('collapsedBroadcast.endAt > :cutoffTime')
            ->addOrderBy('isLive', 'DESC')
            ->addOrderBy('collapsedBroadcast.isRepeat', 'ASC')
            ->addOrderBy('collapsedBroadcast.startAt', 'ASC')
            ->setMaxResults(1)
            ->setParameter('cutoffTime', $cutoffTime);

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
     * Find all upcoming broadcasts of Episodes within the supplied group OR episodes under TLEOs within the
     * supplied group. Ordered by broadcast time ASC.
     *
     * @param int $groupDbId
     * @param DateTimeImmutable $cutoffTime
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function findUpcomingUnderGroup(
        int $groupDbId,
        DateTimeImmutable $cutoffTime,
        ?int $limit,
        int $offset
    ): array {
        /**
         * We're using native queries here due to Doctrine's lack of support for UNION and making certain types of
         * subquery difficult.
         *
         * The actual query part is explained below. Here we are creating the select statement and
         * mapping the result of that query back into Doctrine. This is (mostly automagically) done using the
         * ResultSetMappingBuilder. It's worth noting here that doctrine doesn't really support using
         * MappedSuperClass (the abstraction that splits our core_entity table into brand/series/collection etc.
         * doctrine entities based on type ) in a ResultSetMappingBuilder, so we have to use one of the child entities
         * (in this case Episode) rather than CoreEntity and hack around the "type" field.
         */
        $rsmb = new Query\ResultSetMappingBuilder($this->getEntityManager(), Query\ResultSetMappingBuilder::COLUMN_RENAMING_INCREMENT);
        $rsmb->addRootEntityFromClassMetadata(CollapsedBroadcast::class, 'collapsedBroadcast');
        $rsmb->addJoinedEntityFromClassMetadata(Episode::class, 'coreEntity', 'collapsedBroadcast', 'programmeItem', ['type' => 'magic_type']);
        /**
         * Little bit of magic here to override doctrine's handling of the type field on the CoreEntity MappedSuperClass
         * This makes the "type" field available in the array doctrine returns so our mappers can correctly identify
         * the core entity type.
         */
        $rsmb->addMetaResult('coreEntity', 'magic_type', 'type', true, 'string');
        $rsmb->addJoinedEntityFromClassMetadata(Image::class, 'image', 'coreEntity', 'image');
        $rsmb->addJoinedEntityFromClassMetadata(MasterBrand::class, 'masterBrand', 'coreEntity', 'masterBrand');
        $rsmb->addJoinedEntityFromClassMetadata(Image::class, 'mbImage', 'masterBrand', 'image');
        $rsmb->addJoinedEntityFromClassMetadata(Network::class, 'network', 'masterBrand', 'network');
        $rsmb->addJoinedEntityFromClassMetadata(Image::class, 'nwImage', 'network', 'image');

        // Map raw SQL table aliases to doctrine aliases and have doctrine make the SELECT part of the query
        $selectClause = $rsmb->generateSelectClause([
            'collapsedBroadcast' => 'cb',
            'coreEntity' => 'ce',
            'image' => 'ci',
            'masterBrand' => 'mb',
            'mbImage' => 'mi',
            'network' => 'n',
            'nwImage' => 'ni',
        ]);
        /**
         * It's worth a note to explain what this query actually does. It's not that obvious.
         * The derived table takes all upcoming collapsed broadcasts that are
         * a) broadcasts of episodes directly in the group
         * or b) broadcasts of episodes under TLEOs placed in the group
         * and retrieves them.
         *
         * This derived table (cb) is then left joined to collapsed broadcast (cb2), with a where clause that requires
         * the join to be to a NULL record, and cb2.start_at < cb.start_at. This acts like
         * "GROUP BY cb.programme_item_id", but allows us to pull only the next upcoming broadcast into the group.
         * It's a common enough pattern but not obvious at first glance.
         *
         * The rest of the query is obvious and deals with getting the relevant metadata around the core entity etc.
         */
        $sql = 'SELECT ' . $selectClause;
        $sql .= <<<'EOQ'
            FROM
                (
                    SELECT cb.*
                        FROM membership m
                        INNER JOIN collapsed_broadcast cb ON m.`member_core_entity_id` = cb.`programme_item_id`
                        WHERE m.group_id = :groupDbId AND cb.end_at > :cutoffTime AND cb.is_webcast_only IS NOT 1
                    UNION
                        SELECT cb.*
                        FROM membership m
                        INNER JOIN collapsed_broadcast cb ON m.`member_core_entity_id` = cb.`tleo_id`
                        WHERE m.group_id = :groupDbId AND cb.end_at > :cutoffTime AND cb.is_webcast_only IS NOT 1
                ) AS cb
            LEFT JOIN collapsed_broadcast cb2 ON cb.`programme_item_id` = cb2.`programme_item_id` AND cb.`end_at` > cb2.`end_at` AND cb2.end_at > :cutoffTime
            INNER JOIN core_entity ce ON cb.`programme_item_id` = ce.id
            LEFT JOIN image ci ON ce.image_id = ci.id
            LEFT JOIN master_brand mb ON ce.master_brand_id = mb.id
            LEFT JOIN image mi ON mb.image_id = mi.id
            LEFT JOIN network n ON mb.network_id = n.id
            LEFT JOIN image ni ON n.image_id = ni.id
            WHERE cb2.id IS NULL
            AND ce.is_embargoed = 0
            ORDER BY cb.end_at ASC
            LIMIT :limit
            OFFSET :offset
EOQ;

        $query = $this->getEntityManager()->createNativeQuery($sql, $rsmb);
        $query->setParameter('groupDbId', $groupDbId)
            ->setParameter('cutoffTime', $cutoffTime)
            ->setParameter('limit', $limit)
            ->setParameter('offset', $offset);
        $result = $query->getResult(AbstractQuery::HYDRATE_ARRAY);
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

    private function resolveProgrammeParents(array $result)
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $this->abstractResolveAncestry(
            $result,
            [$repo, 'coreEntityAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
    }
}
