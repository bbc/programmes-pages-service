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
        $statement->bindValue('from', $from);
        $statement->bindValue('to', $to);

        if ($serviceId) {
            $statement->bindValue('serviceId', $serviceId);
        }

        $statement->execute();

        // As this was an SQL query, we need to convert the field names
        // to the Doctrine style
        return $this->sqlResultsToDoctrineResults(
            $statement->fetchAll()
        );
    }

    public function countPlaysForContributorIds(
        array $dbIds,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        int $serviceId = null
    ) {
        $from = '2016-05-24';
        $to = '2016-05-31';

        $sqlQuery = $serviceId ?
            self::SQL_FOR_PLAYS_OF_CONTRIBUTORS_ON_SERVICE :
            self::SQL_FOR_PLAYS_OF_CONTRIBUTORS;

        // Doctrine prepared statement won't accept named parameters when
        // using IN. Just another day in the life of Doctrine
        $params = [$from, $to];
        $types = [\PDO::PARAM_STR, \PDO::PARAM_STR];

        if ($serviceId) {
            $params[] = $serviceId;
            $types[] = \PDO::PARAM_INT;
        }

        $params[] = $dbIds;
        $types[] = \Doctrine\DBAL\Connection::PARAM_INT_ARRAY;

        $statement = $this->getEntityManager()
            ->getConnection()
            ->executeQuery(
                $sqlQuery,
                $params,
                $types
            );

        // As this was an SQL query, we need to convert the field names
        // to the Doctrine style
        return $this->sqlResultsToDoctrineResults(
            $statement->fetchAll()
        );

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

    const SQL_FOR_PLAYS_OF_CONTRIBUTORS = <<<SQL
SELECT c.*, t.plays as contributionPlays FROM contributor c, (
    SELECT straight_join cb.contributor_id AS c_id, 
      COUNT(DISTINCT se.id) AS plays
    FROM broadcast b
    INNER JOIN segment_event se ON b.version_id = se.version_id
    INNER JOIN segment s ON se.segment_id = s.id
    INNER JOIN contribution cb ON s.id = cb.contribution_to_segment_id
    INNER JOIN credit_role cr on cb.credit_role_id = cr.id
    WHERE b.end_at BETWEEN ? AND ?
        AND cr.credit_role_id = 'PERFORMER'
    GROUP BY cb.contributor_id
) t
WHERE t.c_id = c.id
AND t.c_id IN (?)
SQL;

    const SQL_FOR_PLAYS_OF_CONTRIBUTORS_ON_SERVICE = <<<SQL
SELECT c.*, t.plays as contributionPlays FROM contributor c, (
    SELECT straight_join cb.contributor_id AS c_id, 
      COUNT(DISTINCT se.id) AS plays
    FROM broadcast b
    INNER JOIN segment_event se ON b.version_id = se.version_id
    INNER JOIN segment s ON se.segment_id = s.id
    INNER JOIN contribution cb ON s.id = cb.contribution_to_segment_id
    INNER JOIN service sv ON b.service_id = sv.id
    INNER JOIN credit_role cr on cb.credit_role_id = cr.id
    WHERE b.end_at BETWEEN ? AND ?
        AND cr.credit_role_id = 'PERFORMER'
        AND sv.id = ?
    GROUP BY cb.contributor_id
) t
WHERE t.c_id = c.id
AND t.c_id IN (?)
SQL;

}
