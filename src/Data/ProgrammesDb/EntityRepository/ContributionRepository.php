<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ContributionRepository extends EntityRepository
{
    /**
     * @param array $dbIds
     * @param string $type
     * @param bool $getContributionTo
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     * @return mixed
     */
    public function findByContributionTo(
        array $dbIds,
        string $type,
        bool $getContributionTo,
        ?int $limit,
        int $offset
    ): array {
        $columnNameLookup = [
            'programme' => 'contributionToCoreEntity',
            'group' => 'contributionToCoreEntity',
            'segment' => 'contributionToSegment',
            'version' => 'contributionToVersion',
        ];
        $columnName = $columnNameLookup[$type] ?? 'contributionToCoreEntity';

        $qb = $this->createQueryBuilder('contribution')
            ->addSelect(['contributor', 'creditRole', 'thing'])
            ->join('contribution.contributor', 'contributor')
            ->join('contribution.creditRole', 'creditRole')
            ->leftJoin('contributor.thing', 'thing')
            ->andWhere('contribution.' . $columnName . ' IN (:dbIds)')
            ->orderBy('contribution.position')
            ->setFirstResult($offset)
            ->setMaxResults($limit)
            ->setParameter('dbIds', $dbIds);

        if ($getContributionTo) {
            $qb->addSelect('contributionTo')
            ->join('contribution.' . $columnName, 'contributionTo');
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
