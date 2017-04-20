<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

class ContributorRepository extends EntityRepository
{
    public function findByMusicBrainzId(string $musicBrainzId): ?array
    {
        $qb = $this->createQueryBuilder('contributor')
            ->andWhere('contributor.musicBrainzId = :mid')
            ->setParameter('mid', $musicBrainzId)
            // Limit 1 due to duplicate artists with the same musicbrainz id
            ->setMaxResults(1);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findAllMostPlayedWithPlays(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?int $serviceId
    ): array {
        $qb = $this->getPlaysQuery(
            $from,
            $to,
            $serviceId
        );

        // here we want all artists
        $qb->andWhere('contributor.musicBrainzId IS NOT NULL');

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    private function getPlaysQuery(
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        ?int $serviceId
    ): QueryBuilder {
        // In APS this used a sub-query for optimisation.
        // It seems however that the performance
        // of simple joins is good enough for the low traffic use
        // case, so we'll stick with DQL here.
        $qb = $this->createQueryBuilder('contributor')
            ->select([
                'contributor',
                'COUNT(DISTINCT(segmentEvent.id)) as contributorPlayCount',
            ])
            ->join('contributor.contributions', 'contribution')
            ->join('contribution.contributionToSegment', 'segment')
            ->join('segment.segmentEvents', 'segmentEvent')
            ->join('segmentEvent.version', 'version')
            ->join('version.broadcasts', 'broadcast')
            ->join('contribution.creditRole', 'creditRole')
            ->andWhere('broadcast.startAt BETWEEN :from AND :to')
            ->andWhere('creditRole.creditRoleId = \'PERFORMER\'')
            ->groupBy('contributor.id')
            ->orderBy('contributorPlayCount', 'DESC')
            ->addOrderBy('contributor.sortName', 'ASC')
            ->setMaxResults(200)
            ->setParameter('from', $from)
            ->setParameter('to', $to);

        if ($serviceId) {
            $qb->join('broadcast.service', 'service')
                ->andWhere('service.id = :serviceId')
                ->setParameter('serviceId', $serviceId);
        }

        return $qb;
    }
}
