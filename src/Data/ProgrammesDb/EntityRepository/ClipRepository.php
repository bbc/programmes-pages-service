<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;

class ClipRepository extends EntityRepository
{
    /**
     * @param string $parentPid
     * @return Clip[]
     */
    public function findClipsByParentPid(string $parentPid): array
    {
        $queryBuilder = $this->createQueryBuilder('c');
        $queryBuilder
            ->select('c')
            ->innerJoin(
                CoreEntity::class,
                'p',
                Join::WITH,
                $queryBuilder->expr()->andX(
                    $queryBuilder->expr()->eq('c.parent', 'p.id'),
                    $queryBuilder->expr()->eq('p.pid', ':parentPid')
                )
            )
            ->setParameter(':parentPid', $parentPid);

        return $queryBuilder->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
