<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;

class CollapsedBroadcastRepository extends EntityRepository
{
    public const NO_SERVICE = 'NULL';

    use Traits\ParentTreeWalkerTrait;
    use Traits\BroadcastTrait;

    public function countByCategoryAncestryAndEndAtDateRange(
        array $categoryAncestry,
        bool $isWebcastOnly,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): int {
        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->select('COUNT(collapsedBroadcast)')
            ->join('programmeItem.categories', 'category')
            ->andWhere('collapsedBroadcast.endAt > :from')
            ->andWhere('collapsedBroadcast.endAt <= :to')
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->andWhere('category.ancestry LIKE :categoryAncestry')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->setParameter('from', $from)
            ->setParameter('to', $to)
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('categoryAncestry', $this->ancestryIdsToString($categoryAncestry) . '%');

        return $qb->getQuery()->getSingleScalarResult();
    }

    public function countUpcomingByProgramme(
        array $ancestry,
        bool $isWebcastOnly,
        DateTimeImmutable $from
    ): int {
        $qb = $this->createQueryBuilder('collapsedBroadcast', false)
            ->select('COUNT(collapsedBroadcast)')
            ->andWhere('collapsedBroadcast.endAt > :from')
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->andWhere('programmeItem.ancestry LIKE :ancestryClause')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('from', $from)
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');

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

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
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

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
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

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['programmeItem', 'ancestry']
        );
    }

    private function createCollapsedBroadcastsOfProgrammeQueryBuilder(array $ancestry, bool $isWebcastOnly): QueryBuilder
    {
        return $this->createQueryBuilder('collapsedBroadcast', false)
            ->addSelect(['programmeItem', 'image', 'masterBrand', 'mbNetwork'])
            ->leftJoin('programmeItem.image', 'image')
            ->leftJoin('programmeItem.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'mbNetwork')
            ->andWhere('programmeItem.ancestry LIKE :ancestryClause')
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->andWhere('collapsedBroadcast.isWebcastOnly = :isWebcastOnly')
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');
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
            ->setParameter('isWebcastOnly', $isWebcastOnly)
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');
    }

    private function explodeFields(array $result): array
    {
        $result = $this->explodeField($result, 'serviceIds');
        $result = $this->explodeField($result, 'broadcastIds');
        return $this->explodeField($result, 'areWebcasts');
    }
}
