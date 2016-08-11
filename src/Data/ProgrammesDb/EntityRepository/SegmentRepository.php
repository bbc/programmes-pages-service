<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SegmentRepository extends EntityRepository
{
    public function findByPidFull(string $pid)
    {
        $qb = $this->createQueryBuilder('segment')
            ->andWhere('segment.pid = :pid')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
