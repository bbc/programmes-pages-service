<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ContributionRepository extends EntityRepository
{
    use Traits\SetLimitTrait;

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
            ->addSelect(['contributor', 'creditRole'])
            ->join('contribution.contributor', 'contributor')
            ->join('contribution.creditRole', 'creditRole')
            ->andWhere('contribution.' . $columnName . ' IN (:dbIds)')
            ->orderBy('contribution.position')
            ->setFirstResult($offset)
            ->setParameter('dbIds', $dbIds);

        $qb = $this->setLimit($qb, $limit);

        if ($getContributionTo) {
            $qb->addSelect('contributionTo')
            ->join('contribution.' . $columnName, 'contributionTo');
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
