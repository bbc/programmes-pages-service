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
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findByGroup(string $groupId): array
    {
        $qb = $this->createQueryBuilder('image')
            ->innerJoin('ProgrammesPagesService:Membership', 'membership', Query\Expr\Join::WITH, 'membership.memberImage = image')
            ->where('IDENTITY(membership.group) = :group_id')
            ->orderBy('membership.position', 'ASC')
            ->setParameter('group_id', $groupId);
        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
