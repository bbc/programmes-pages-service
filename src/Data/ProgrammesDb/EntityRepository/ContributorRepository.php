<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class ContributorRepository extends EntityRepository
{
    public function findByMusicBrainzId(string $musicBrainzId)
    {
        $qb = $this->createQueryBuilder('contributor')
            ->where('contributor.musicBrainzId = :mid')
            ->setParameter('mid', $musicBrainzId);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findAllMostPlayedWithPlays(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        int $serviceId = null
    ): array {
        $qb = $this->getPlaysQuery(
            $from,
            $to,
            $serviceId
        );

        // here we want all artists
        $qb->andWhere('contributor.musicBrainzId IS NOT NULL');

        $results = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $results;
    }

    public function countPlaysForContributorIds(
        array $dbIds,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        int $serviceId = null
    ): array {
        $qb = $this->getPlaysQuery(
            $from,
            $to,
            $serviceId
        );

        // here we only want the artists we were looking for
        $qb->andWhere('contributor.id IN(:ids)')
            ->setParameter('ids', $dbIds);

        $results = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $results;
    }

    private function getPlaysQuery(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        int $serviceId = null
    ): QueryBuilder {
        // In APS this used a sub-query for optimisation.
        // It seems however that the performance
        // of simple joins is good enough for the low traffic use
        // case, so we'll stick with DQL here.
        $qb = $this->createQueryBuilder('contributor')
            ->select([
                'contributor',
                'COUNT(DISTINCT(se.id)) as contributionPlays',
            ])
            ->join('contributor.contributions', 'cb')
            ->join('cb.contributionToSegment', 's')
            ->join('s.segmentEvents', 'se')
            ->join('se.version', 'v')
            ->join('v.broadcasts', 'b')
            ->join('cb.creditRole', 'cr')
            ->where('b.endAt BETWEEN :from AND :to')
            ->andWhere('cr.creditRoleId = \'PERFORMER\'')
            ->groupBy('contributor')
            ->orderBy('contributionPlays', 'DESC')
            ->addOrderBy('contributor.name')
            ->setMaxResults(200)
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        if ($serviceId) {
            $qb->join('b.service', 'sv')
                ->andWhere('sv.id = :serviceId')
                ->setParameter('serviceId', $serviceId);
        }

        return $qb;
    }
}
