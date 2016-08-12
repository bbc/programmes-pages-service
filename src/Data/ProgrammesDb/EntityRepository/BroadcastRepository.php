<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use Doctrine\ORM\Query;

class BroadcastRepository extends EntityRepository
{
    /**
     * @return Broadcast[]
     */
    public function findBroadcastsOfVersionId(int $versionId)
    {
        return $this->createQueryBuilder('broadcast')
            ->addSelect(['service'])
            ->join('broadcast.service', 'service')
            ->where("broadcast.version = :versionDbId")
            ->setParameter('versionDbId', $versionId)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }
}
