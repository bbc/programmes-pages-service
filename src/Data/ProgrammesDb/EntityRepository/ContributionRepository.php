<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ContributionRepository extends EntityRepository
{
    public function findByContributionTo(array $dbIds, string $type, bool $getContributionTo, $limit, int $offset)
    {
        $columnNameLookup = [
            'programme' => 'contributionToCoreEntity',
            'group' => 'contributionToCoreEntity',
            'segment' => 'contributionToSegment',
            'version' => 'contributionToVersion',
        ];
        $columnName = $columnNameLookup[$type] ?? 'contributionToCoreEntity';

        $qb = $this->createQueryBuilder('contribution')
            ->addSelect(['contributor', 'creditRole'])
            ->join('contribution.contributor', 'contributor')
            ->join('contribution.creditRole', 'creditRole')
            ->andWhere('contribution.' . $columnName . ' IN (:dbIds)')
            ->orderBy('contribution.position')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('dbIds', $dbIds);

        if ($getContributionTo) {
            $qb->addSelect('contributionTo')
            ->join('contribution.' . $columnName, 'contributionTo');
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
