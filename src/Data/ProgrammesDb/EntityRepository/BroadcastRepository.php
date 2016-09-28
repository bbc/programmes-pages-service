<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use InvalidArgumentException;

class BroadcastRepository extends EntityRepository
{
    use Traits\SetLimitTrait;

    /**
     * @param array $dbIds
     * @param string $type
     * @param int|AbstractService::NO_LIMIT $limit
     * @param int $offset
     * @return mixed
     */
    public function findByVersion(array $dbIds, string $type, $limit, int $offset)
    {
        if (!in_array($type, ['Broadcast', 'Webcast', 'Any'])) {
            throw new InvalidArgumentException(sprintf(
                'Called findByVersion with an invalid type. Expected one of "%s", "%s" or "%s" but got "%s"',
                'Broadcast',
                'Webcast',
                'Any',
                $type
            ));
        }

        $typeLookup = [
            'Broadcast' => false,
            'Webcast' => true,
            'Any' => null,
        ];
        $isWebcast = $typeLookup[$type] ?? null;

        $qb = $this->createQueryBuilder('broadcast')
            ->addSelect(['service', 'network'])
            ->join('broadcast.service', 'service')
            ->join('service.network', 'network')
            ->andWhere("broadcast.version IN (:dbIds)")
            ->addOrderBy('broadcast.startAt', 'DESC')
            ->addOrderBy('service.sid', 'DESC')
            ->setFirstResult($offset)
            ->setParameter('dbIds', $dbIds);

        $qb = $this->setLimit($qb, $limit);

        if (!is_null($isWebcast)) {
            $qb->andWhere('broadcast.isWebcast = :isWebcast')
                ->setParameter('isWebcast', $isWebcast);
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findAllYearsAndMonthsByProgramme(array $ancestry)
    {
        $qb = $this->createQueryBuilder('broadcast')
            ->select(['DISTINCT YEAR(broadcast.startAt) as year', 'MONTH(broadcast.startAt) as month'])
            ->andWhere('programmeItem INSTANCE OF ProgrammesPagesService:Episode')
            ->andWhere("programmeItem.ancestry LIKE :ancestryClause")
            ->addOrderBy('year', 'DESC')
            ->addOrderBy('month', 'DESC')
            ->setParameter('ancestryClause', $this->ancestryIdsToString($ancestry) . '%');

        return $qb->getQuery()->getResult(Query::HYDRATE_SCALAR);
    }

    public function createQueryBuilder($alias, $joinViaVersion = true, $indexBy = null)
    {
        // Any time Broadcasts are fetched here they must be inner joined to
        // their programme entity - either directly - or via the version, this
        // allows the embargoed filter to trigger and exclude unwanted items.
        // This ensures that Broadcasts that belong to a version that belongs
        // to an embargoed programme are never returned
        if ($joinViaVersion) {
            return parent::createQueryBuilder($alias)
                ->join($alias . '.version', 'version')
                ->join('version.programmeItem', 'programmeItem');
        }

        return parent::createQueryBuilder($alias)
            ->join($alias . '.programmeItem', 'programmeItem');
    }

    private function ancestryIdsToString(array $ancestry)
    {
        return implode(',', $ancestry) . ',';
    }
}
