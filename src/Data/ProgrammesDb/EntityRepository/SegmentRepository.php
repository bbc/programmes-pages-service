<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SegmentRepository extends EntityRepository
{
    public function findByPidFull(string $pid)
    {
        $qb = $this->createQueryBuilder('segment')
            ->addSelect(['contribution', 'contributor', 'creditRole'])
            ->leftJoin('segment.contributions', 'contribution')
            ->leftJoin('contribution.contributor', 'contributor')
            ->leftJoin('contribution.creditRole', 'creditRole')
            ->andWhere('segment.pid = :pid')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
