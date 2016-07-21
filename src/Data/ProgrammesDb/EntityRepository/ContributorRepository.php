<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits\SqlToDoctrineTrait;
use DateTimeImmutable;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class ContributorRepository extends EntityRepository
{
    use SqlToDoctrineTrait;

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
        $from = '2016-06-01';
        $to = '2016-06-07';

        /*
         * Here I need to use a sub-query. The sub-query counts the number of
         * plays of a contributor in the given time window. The outer query
         * then uses those values to get the contributors with those counts,
         * ordered by the most common. Doctrine doesn't natively support a
         * sub-query in the FROM clause. However, I don't need to use Doctrine's
         * model mapping as I don't actually need the values of the joins.
         * Therefore, I can run a native SQL query.
         */
        $sqlQuery = $serviceId ?
            self::SQL_FOR_PLAYS_WITH_SERVICE : self::SQL_FOR_PLAYS;

        $statement = $this->getEntityManager()
            ->getConnection()
            ->prepare($sqlQuery);
        $statement->bindParam('from', $from);
        $statement->bindParam('to', $to);

        if ($serviceId) {
            $statement->bindParam('serviceId', $serviceId);
        }

        $statement->execute();

        // As this was an SQL query, we need to convert the field names
        // to the Doctrine style
        return $this->sqlResultsToDoctrineResults(
            $statement->fetchAll()
        );



        $data = [];

//        foreach ($results as $result) {
//            $data[] = (object) [
//                'contributor' =>
//            ]
//        }
//
//        return $results;
//        var_dump($data);die;



//        $innerQuery = $this->getEntityManager()
//            ->getRepository('ProgrammesPagesService:Contribution')
//            ->buildPlaysForContributorQuery($from, $to, $serviceId);
//
//
//        $qb = $this->createQueryBuilder('contributor')
//            ->select(['contributor', 't.plays'])
//            ->from('(' . $innerQuery->getDQL() . ')', 't');

//        echo($qb->getQuery()->getSQL());die;

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

    const SQL_FOR_PLAYS = <<<SQL
SELECT c.*, t.plays as contributionPlays FROM contributor c, (
    SELECT straight_join cb.contributor_id AS c_id,
        COUNT(DISTINCT se.id) AS plays
    FROM broadcast b
    INNER JOIN segment_event se ON b.version_id = se.version_id
    INNER JOIN segment s ON se.segment_id = s.id
    INNER JOIN contribution cb ON s.id = cb.contribution_to_segment_id
    INNER JOIN credit_role cr on cb.credit_role_id = cr.id
    WHERE b.end_at BETWEEN :from AND :to
        AND cr.credit_role_id = 'PERFORMER'
    GROUP BY cb.contributor_id
) t
WHERE t.c_id = c.id
    AND c.music_brainz_id IS NOT NULL
ORDER BY plays DESC, c.name
LIMIT 200;
SQL;

    const SQL_FOR_PLAYS_WITH_SERVICE = <<<SQL
SELECT c.*, t.plays as contributionPlays FROM contributor c, (
    SELECT straight_join cb.contributor_id AS c_id,
      COUNT(DISTINCT se.id) AS plays
    FROM broadcast b
    INNER JOIN segment_event se ON b.version_id = se.version_id
    INNER JOIN segment s ON se.segment_id = s.id
    INNER JOIN contribution cb ON s.id = cb.contribution_to_segment_id
    INNER JOIN service sv ON b.service_id = sv.id
    INNER JOIN credit_role cr on cb.credit_role_id = cr.id
    WHERE b.end_at BETWEEN :from AND :to
        AND cr.credit_role_id = 'PERFORMER'
        AND sv.id = :serviceId
    GROUP BY cb.contributor_id
) t
WHERE t.c_id = c.id
  AND c.music_brainz_id IS NOT NULL
ORDER BY plays DESC, c.name
LIMIT 200;
SQL;
}
