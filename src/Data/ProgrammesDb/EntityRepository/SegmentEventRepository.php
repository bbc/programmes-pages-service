<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\SegmentEvent;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;

class SegmentEventRepository extends EntityRepository
{
    use Traits\ParentTreeWalkerTrait;
    use Traits\SetLimitTrait;

    public function findByPid(string $pid)
    {
        $qb = $this->createQueryBuilder('segmentEvent')
            ->addSelect(['segment', 'version'])
            ->join('segmentEvent.segment', 'segment')
            ->andWhere('segmentEvent.pid = :pid')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findByPidFull(string $pid)
    {
        $qb = $this->createQueryBuilder('segmentEvent')
            ->addSelect(['segment', 'version', 'programmeItem', 'image', 'masterBrand', 'network'])
            ->join('segmentEvent.segment', 'segment')
            ->leftJoin('programmeItem.image', 'image')
            ->leftJoin('programmeItem.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->andWhere('segmentEvent.pid = :pid')
            ->setParameter('pid', $pid);

        return $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    /**
     * @param array $dbIds
     * @param int|null $limit
     * @param $offset
     * @return SegmentEvent[]
     */
    public function findByVersionWithContributions(array $dbIds, $limit, int $offset)
    {
        $qb = $this->createQueryBuilder('segment_event')
            ->addSelect([
                'segment',
                'contributions',
                'contributor',
                'creditRole',
            ])
            ->join('segment_event.segment', 'segment')
            ->leftJoin('segment.contributions', 'contributions')
            ->leftJoin('contributions.contributor', 'contributor')
            ->leftJoin('contributions.creditRole', 'creditRole')
            ->where("segment_event.version IN (:dbIds)")
            ->addOrderBy('segment_event.position', 'ASC')
            ->setFirstResult($offset)
            ->setParameter('dbIds', $dbIds);

        $qb = $this->setLimit($qb, $limit);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

    }

    /**
     * @param int $contributorId
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function findFullLatestBroadcastedForContributor(int $contributorId, $limit, int $offset)
    {
        $qb = $this->createQueryBuilder('segmentEvent')
            // fetching full, so we need a big select to return details
            ->select([
                'DISTINCT segmentEvent',
                'segment',
                'version',
                'programmeItem',
            ])
            ->join('segmentEvent.segment', 'segment')
            ->join('version.broadcasts', 'broadcast')
            // this is a left join, as there can be many contributions
            ->leftJoin('segment.contributions', 'contribution')
            ->andWhere('contribution.contributor = :id')
            ->setFirstResult($offset)
            // now we're going to order using the broadcast
            // first by the most recent broadcast date
            // then by the segmentEvent offset/position in case
            // there were several by this artist in one episode
            ->addOrderBy('broadcast.startAt', 'DESC')
            ->addOrderBy('segmentEvent.offset', 'DESC')
            ->addOrderBy('segmentEvent.position', 'DESC')
            ->setParameter('id', $contributorId);

        $qb = $this->setLimit($qb, $limit);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['version', 'programmeItem', 'ancestry']
        );
    }

    /**
     * @param array $dbIds
     * @param bool $groupByVersionId
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function findBySegmentFull(array $dbIds, bool $groupByVersionId, $limit, int $offset) : array
    {
        $qb = $this->createQueryBuilder('segmentEvent')
            ->addSelect(['version', 'programmeItem', 'image', 'masterBrand', 'network'])
            // masterBrand needs to be fetched to get ownership details
            ->leftJoin('programmeItem.masterBrand', 'masterBrand')
            // fetching image pid
            ->leftJoin('programmeItem.image', 'image')
            // network needs to be fetched in order to create masterBrand
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('version.broadcasts', 'broadcast')
            ->andWhere('segmentEvent.segment IN (:dbIds)')
            // versions that have been broadcast come first
            ->addSelect('CASE WHEN broadcast.startAt IS NULL THEN 1 ELSE 0 AS HIDDEN hasBroadcast')
            ->setFirstResult($offset)
            ->addOrderBy('hasBroadcast', 'ASC')
            // oldests broadcasts come first
            ->addOrderBy('broadcast.startAt', 'ASC')
            // alphabetical ordering by title
            ->addOrderBy('programmeItem.title', 'ASC')
            ->setParameter('dbIds', $dbIds);

        $qb = $this->setLimit($qb, $limit);

        if ($groupByVersionId) {
            $qb->addGroupBy('version.id');
        }

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->abstractResolveAncestry(
            $result,
            [$this, 'programmeAncestryGetter'],
            ['version', 'programmeItem', 'ancestry']
        );
    }

    /**
     * @param array $dbIds
     * @param bool $groupByVersionId
     * @param int|null $limit
     * @param int $offset
     * @return array
     */
    public function findBySegment(array $dbIds, bool $groupByVersionId, $limit, int $offset) : array
    {
        $qb = $this->createQueryBuilder('segmentEvent')
            ->addSelect(['version', 'programmeItem'])
            ->andWhere('segmentEvent.segment IN (:dbIds)')
            ->setFirstResult($offset)
            ->setParameter('dbIds', $dbIds);

        if ($groupByVersionId) {
            $qb->addGroupBy('version.id');
        }

        $qb = $this->setLimit($qb, $limit);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
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

    private function programmeAncestryGetter(array $ids)
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        return $repo->findByIds($ids);
    }
}
