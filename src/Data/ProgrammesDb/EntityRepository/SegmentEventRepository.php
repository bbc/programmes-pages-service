<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Broadcast;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Contribution;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Segment;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

class SegmentEventRepository extends EntityRepository
{
    public function findByPid(string $pid)
    {
        $qb = $this->createQueryBuilder('segmentEvent')
            ->addSelect(['segment, version'])
            ->join('segmentEvent.segment', 'segment')
            ->andWhere('segmentEvent.pid = :pid')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findFullLatestBroadcastedForContributor(
        int $contributorId,
        int $limit,
        int $offset
    ) {
        /* APS Query:
        SELECT DISTINCT se.*, s.*
        FROM segment_events se
        JOIN segments s ON (se.segment_id = s.id)
        LEFT JOIN contributions c ON s.id = c.segment_id
        JOIN versions v ON v.id = se.version_id
        JOIN broadcasts b ON b.version_id = v.id
        WHERE c.contributor_id = ?
        ORDER BY b.start DESC, DATE_ADD(b.start, INTERVAL se.version_offset SECOND) DESC, se.position DESC
        LIMIT 50
         */


        $qb = $this->createQueryBuilder('segmentEvent')
            // fetching full, so we need a big select to return details
            ->select('DISTINCT segmentEvent')
            ->addSelect(
                'segment',
                'version',
                'programmeItem'
            )
            ->join(
                Segment::class,
                'segment',
                Join::WITH,
                'segmentEvent.segment = segment.id'
            )
            // this is a left join, as there can be many contributions
            ->join(
                Contribution::class,
                'contribution',
                Join::WITH,
                'contribution.contributionToSegment = segment.id'
            )
            // now join to the broadcast for ordering
            ->join(
                Broadcast::class,
                'broadcast',
                Join::WITH,
                'broadcast.version = version.id'
            )
            ->andWhere('contribution.contributor = :id')
            // now we're going to order using the broadcast
            // first by the most recent broadcast date
            // then by the segmentEvent offset/position in case
            // there were several by this artist in one episode
            ->addOrderBy('broadcast.startAt', 'DESC')
            ->addOrderBy('segmentEvent.offset', 'DESC')
            ->addOrderBy('segmentEvent.position', 'DESC')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('id', $contributorId);

        $r = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        var_dump($r);die;
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        // Any time SegmentEvents are fetched here they must be inner joined to
        // their programme entity, this allows the embargoed filter to trigger
        // and exclude unwanted items.
        // This ensures that SegmentEvents that belong to a version that belongs
        // to an embargoed programme are never returned
        return parent::createQueryBuilder($alias)
            ->join($alias . '.version', 'version')
            ->join('version.programmeItem', 'programmeItem');
    }
}
