<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use DateTimeImmutable;
use Doctrine\ORM\QueryBuilder;
use InvalidArgumentException;

class BroadcastRepository extends EntityRepository
{
    use Traits\ParentTreeWalkerTrait;
    use Traits\NetworkMediumTrait;
    use Traits\BroadcastTrait;

    public function findByVersion(array $dbIds, string $type, ?int $limit, int $offset): array
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
            ->setMaxResults($limit)
            ->setParameter('dbIds', $dbIds);

        $this->setEntityTypeFilter($qb, $type);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findAllYearsAndMonthsByProgramme(array $ancestry, string $type): array
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

    public function findBroadcastedDatesForCategories(
        array $categoryAncestries,
        string $type,
        ?string $medium,
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

        if ($medium) {
            $this->assertNetworkMedium($medium);

            $qb->join('broadcast.service', 'service')
                ->innerJoin('service.network', 'networkOfService')
                ->andWhere('networkOfService.medium = :medium')
                ->setParameter('medium', $medium);
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_SCALAR);
    }

    private function entityTypeFilterValue(string $type): ?bool
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

    private function setEntityTypeFilter(QueryBuilder $qb, string $type, string $broadcastAlias = 'broadcast'): QueryBuilder
    {
        $isWebcast = $this->entityTypeFilterValue($type);

        if (!is_null($isWebcast)) {
            $qb->andWhere($broadcastAlias . '.isWebcast = :isWebcast')
                ->setParameter('isWebcast', $isWebcast);
        }

        return $qb;
    }
}
