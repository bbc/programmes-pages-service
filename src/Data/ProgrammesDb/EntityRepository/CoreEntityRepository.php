<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;

class CoreEntityRepository extends MaterializedPathRepository
{
    /**
     * Full Find By Pid
     *
     * This resolves all parents
     *
     * @param  string $pid
     * @return array|null
     */
    public function findByPidFull($pid)
    {
        $qb = $this->createQueryBuilder('programme')
            ->addSelect(['image', 'masterBrand', 'network'])
            ->leftJoin('programme.image', 'image')
            ->leftJoin('programme.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->where('programme.pid = :pid')
            ->setParameter('pid', $pid);
        $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        return $result ? $this->resolveParents([$result])[0] : $result;
    }

    public function findAllWithParents($limit, $offset)
    {
        $qb = $this->createQueryBuilder('programme')
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    public function countAll()
    {
        $qb = $this->createQueryBuilder('programme')
            ->select(['count(programme.id)']);
        return $qb->getQuery()->getSingleScalarResult();
    }

    public function findEpisodeGuideChildren($dbId, $limit, $offset)
    {
        $qText = <<<QUERY
SELECT programme, image
FROM ProgrammesPagesService:Programme programme
LEFT JOIN programme.image image
LEFT JOIN ProgrammesPagesService:ProgrammeItem pi WITH programme.id = pi.id
WHERE programme.parent = :dbId
AND programme INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode)
ORDER BY programme.position DESC, pi.releaseDate DESC, programme.title ASC
QUERY;

        $q = $this->getEntityManager()->createQuery($qText)
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('dbId', $dbId);

        $result = $q->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    public function countEpisodeGuideChildren($dbId)
    {
        $qText = <<<QUERY
SELECT count(programme.id)
FROM ProgrammesPagesService:Programme programme
WHERE programme.parent = :dbId
AND programme INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode)
QUERY;

        $q = $this->getEntityManager()->createQuery($qText)
            ->setParameter('dbId', $dbId);

        return $q->getSingleScalarResult();
    }

    public function findDescendants($programme, $limit, $offset)
    {
        $qb = $this->getChildrenQueryBuilder($programme)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    /**
     * Takes an array of Programme array representations, finds all the
     * ancestors for all the programmes and attaches the full entities onto the
     * programmes.
     *
     * @param  array  $programmes
     * @return array
     */
    private function resolveParents(array $programmes)
    {
        // Build a list of all unique parentIds found in all of the programmes
        $listOfAllParentIds = [];
        foreach ($programmes as $programme) {
            foreach ($this->getParentIdsFromAncestry($programme['ancestry']) as $parentId) {
                $listOfAllParentIds[$parentId] = true;
            }
        }
        $listOfAllParentIds = array_keys($listOfAllParentIds);

        // No parents so do nothing
        if (empty($listOfAllParentIds)) {
            return $programmes;
        }

        // Get all said programmes from the DB.
        $parentProgrammes = $this->createQueryBuilder('programme')
            ->addSelect(['image', 'masterBrand', 'network'])
            ->leftJoin('programme.image', 'image')
            ->leftJoin('programme.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->where("programme.id IN(:ids)")
            ->setParameter('ids', $listOfAllParentIds)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);

        // Process the programmes into an array (functionally), with their ancestry combined
        $processedProgrammes = [];
        foreach ($programmes as $programme) {
            $processedProgrammes[] = $this->combineAncestry($programme, $parentProgrammes);
        }

        return $processedProgrammes;
    }

    /**
     * Using the potential ancestors as a source, recursively set the parents into place
     * @param array $programme
     * @param array $potentialAncestors
     * @return array
     */
    private function combineAncestry(
        $programme,
        array $potentialAncestors = []
    ) {
        // an embargoed ancestor will come through as null
        if (is_null($programme)) {
            return null;
        }
        $parentIds = $this->getParentIdsFromAncestry($programme['ancestry']);
        if ($parentIds) {
            $resolvedParent = $this->searchSetForProgrammeWithId($potentialAncestors, end($parentIds));
            $programme['parent'] = $this->combineAncestry($resolvedParent, $potentialAncestors);
        }
        return $programme;
    }

    private function searchSetForProgrammeWithId(array $resultSet, int $id)
    {
        // TODO we should come up with a way of storing all programmes using
        // their ID as a key, so we can lookup a programme from it's ID in
        // O(1) time, instead of O(n)
        foreach ($resultSet as $programme) {
            if ($programme['id'] == $id) {
                return $programme;
            }
        }
        return null;
    }

    private function getParentIdsFromAncestry(string $ancestry): array
    {
        // $ancestry contains a string of all IDs including the current one
        // Thus for parent ids we want an array of all but the last item (which
        // is the current id)
        $ancestors = explode(',', $ancestry, -2);
        return $ancestors ?? [];
    }
}
