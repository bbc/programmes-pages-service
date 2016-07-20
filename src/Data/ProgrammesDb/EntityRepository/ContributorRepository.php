<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use DateTimeImmutable;
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

    public function findAllMostPlayed(
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        int $serviceId = null
    ) {

        $innerQuery = $this->getEntityManager()
            ->getRepository('ProgrammesPagesService:Contribution')
            ->buildPlaysForContributorQuery($start, $end, $serviceId);


        $qb = $this->createQueryBuilder('contributor')
            ->select(['contributor', 't.plays'])
            ->from('(' . $innerQuery->getDQL() . ')', 't');

        echo($qb->getQuery()->getSQL());die;

        /* QUERY REQUIRED
        SELECT c.*, t.plays FROM contributor c, (
           SELECT straight_join cb.contributor_id AS c_id, COUNT(DISTINCT se.id) AS plays
           FROM broadcast b
             INNER JOIN segment_event se ON b.version_id = se.version_id
             INNER JOIN segment s ON se.segment_id = s.id
             INNER JOIN contribution cb ON s.id = cb.contribution_to_segment_id
             INNER JOIN service sv ON b.service_id = sv.id
             INNER JOIN credit_role cr on cb.credit_role_id = cr.id
           WHERE b.end_at BETWEEN '2016-06-01' AND '2016-06-07'
             AND cr.credit_role_id = 'PERFORMER'
             AND sv.parent_service_id = ?
           GROUP BY cb.contributor_id
         ) t
        WHERE t.c_id = c.id
              AND c.music_brainz_id IS NOT NULL
        ORDER BY plays DESC, c.name
        LIMIT 100
        */

        /* APS QUERY
        SELECT c.*, t.plays FROM contributors c, (
            SELECT straight_join cb.contributor_id AS c_id, COUNT(DISTINCT se.id) AS plays
            FROM broadcasts b
            INNER JOIN segment_events se ON b.version_id = se.version_id
            INNER JOIN segments s ON se.segment_id = s.id
            INNER JOIN contributions cb ON s.id = cb.segment_id
            INNER JOIN services sv ON b.service_id = sv.id
            WHERE b.is_recent = 1
            AND cb.credit_role_id = 7
            AND sv.parent_service_id = ?
            AND b.schedule_date BETWEEN ? AND ?
            GROUP BY cb.contributor_id
          ) t
      WHERE t.c_id = c.id
      AND c.musicbrainz_gid IS NOT NULL
      ORDER BY plays DESC, c.name
      LIMIT ?$\d+$
        */

    }
}
