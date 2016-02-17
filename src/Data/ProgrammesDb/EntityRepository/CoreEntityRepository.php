<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Programme;
use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use InvalidArgumentException;

class CoreEntityRepository extends MaterializedPathRepository
{
    const PROGRAMME_ENTITY_TYPE_LIST = "(" .
        'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Brand,' .
        'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Series,' .
        'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Episode,' .
        'BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip' .
    ")";

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

    public function findChildren($programmeId, $limit, $offset)
    {
        $qb = $this->createQueryBuilder('programme')
            ->addSelect(['image'])
            ->leftJoin('programme.image', 'image')
            ->where('programme.parent = :parentId')
            ->setMaxResults($limit)
            ->setFirstResult($offset)
            ->setParameter('parentId', $programmeId);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function countChildren($programmeId)
    {
        $qb = $this->createQueryBuilder('programme')
            ->select(['count(programme.id)'])
            ->where('programme.parent = :parentId')
            ->setParameter('parentId', $programmeId);

        return $qb->getQuery()->getSingleScalarResult();
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
        array $programme,
        array $potentialAncestors = []
    ): array {
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
        return array_slice(explode(',', $ancestry), 0, -1);
    }
}
