<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ContributorRepository extends EntityRepository
{
    public function findByMusicBrainzId(string $musicBrainzId)
    {
        $qb = $this->createQueryBuilder('contributor')
            ->where('contributor.musicBrainzId = :mid')
            ->setParameter('mid', $musicBrainzId);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }
}
