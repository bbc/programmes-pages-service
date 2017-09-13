<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ImageRepository extends EntityRepository
{
    public function findByPid(string $pid): ?array
    {
        $qb = parent::createQueryBuilder('image')
            ->andWhere('image.pid = :pid')
            ->andWhere('image.isEmbargoed = 0')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
