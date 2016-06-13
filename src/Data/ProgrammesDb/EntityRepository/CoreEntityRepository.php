<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use InvalidArgumentException;

class CoreEntityRepository extends MaterializedPathRepository
{
    use Traits\ParentTreeWalkerTrait;

    /**
     * Get an entity's ID, based upon its PID
     * Used when a page wants to find out about data related to an entity, but
     * doesn't need the entity itself.
     *
     * @param string $pid        The pid to lookup
     * @param string $entityType Filter results by "Programme", "Group" or "CoreEntity"
     * @return int|null
     */
    public function findIdByPid(string $pid, string $entityType = 'CoreEntity')
    {
        if (!in_array($entityType, ['Programme', 'Group', 'CoreEntity'])) {
            throw new InvalidArgumentException(sprintf(
                'Called findByPidFull with an invalid type. Expected one of "%s", "%s" or "%s" but got "%s"',
                'Programme',
                'Group',
                'CoreEntity',
                $entityType
            ));
        }

        $qText = <<<QUERY
SELECT entity.id
FROM ProgrammesPagesService:$entityType entity
WHERE entity.pid = :pid
QUERY;

        $q = $this->getEntityManager()->createQuery($qText)
            ->setParameter('pid', $pid);

        $result = $q->getOneOrNullResult(Query::HYDRATE_SINGLE_SCALAR);
        return !is_null($result) ? (int) $result : null;
    }

    /**
     * Full Find By Pid
     *
     * This resolves all parents
     *
     * @param string $pid        The pid to lookup
     * @param string $entityType Filter results by "Programme", "Group" or "CoreEntity" to not filter
     * @return array|null
     */
    public function findByPidFull(string $pid, string $entityType = 'CoreEntity')
    {
        if (!in_array($entityType, ['Programme', 'Group', 'CoreEntity'])) {
            throw new InvalidArgumentException(sprintf(
                'Called findByPidFull with an invalid type. Expected one of "%s", "%s" or "%s" but got "%s"',
                'Programme',
                'Group',
                'CoreEntity',
                $entityType
            ));
        }

        // YIKES! categories is a many-to-many join, that could result in
        // an increase of rows returned by the DB and the potential for slow DB
        // queries as per https://ocramius.github.io/blog/doctrine-orm-optimization-hydration/.
        // Except it doesn't - the majority of Programmes have less than 3
        // categories. At time of writing this comment (June 2016) only 9% of
        // the Programmes in PIPS have 3 or more Categories and the most
        // Categories a Programme has is 12. Creating an few extra rows in
        // rare-ish cases is way more efficient that having to do a two-step
        // hydration process.

        $qb = $this->getEntityManager()->createQueryBuilder()
            ->select(['entity', 'image', 'masterBrand', 'network', 'category'])
            ->from('ProgrammesPagesService:' . $entityType, 'entity') // For filtering on type
            ->leftJoin('entity.image', 'image')
            ->leftJoin('entity.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('entity.categories', 'category')
            ->where('entity.pid = :pid')
            ->setParameter('pid', $pid);

        $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        $withHydratedParents = $result ? $this->resolveParents([$result], true)[0] : $result;
        return $withHydratedParents ? $this->resolveCategories([$withHydratedParents])[0] : $withHydratedParents;
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

    public function findImmediateSibling(Programme $programme, string $direction)
    {
        if (!in_array($direction, ['next', 'previous'])) {
            throw new InvalidArgumentException(sprintf(
                'Called findImmediateSibling with an invalid direction type. Expected one of "%s" or "%s" but got "%s"',
                'next',
                'previous',
                $direction
            ));
        }

        // Programmes that don't have a parent, can't have any siblings
        if (!$programme->getParent()) {
            return null;
        }

        $isNext = $direction == 'next';
        $orderDirection = $isNext ? 'ASC' : 'DESC';
        $filterOperation = $isNext ? '>' : '<' ;

        // First try and look up based on position
        if (!is_null($programme->getPosition())) {
            $qb = $this->getEntityManager()->createQueryBuilder()
                ->select(['programme'])
                ->from('ProgrammesPagesService:' . $this->dbType($programme), 'programme')
                ->andWhere('programme.parent = :parentDbId')
                ->andWhere('programme.position ' . $filterOperation . ' :originalPosition')
                ->orderBy('programme.position', $orderDirection)
                ->setMaxResults(1)
                ->setParameter('parentDbId', $programme->getParent()->getDbId())
                ->setParameter('originalPosition', $programme->getPosition());

            $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);

            if ($result) {
                return $result;
            }
        }

        // Else attempt to look up based on ReleaseDate (which only exists for ProgrammeItems)
        if ($programme instanceof ProgrammeItem && !is_null($programme->getReleaseDate())) {
            $qb = $this->getEntityManager()->createQueryBuilder()
                ->select(['programme'])
                ->from('ProgrammesPagesService:' . $this->dbType($programme), 'programme')
                ->andWhere('programme.parent = :parentDbId')
                ->andWhere('programme.releaseDate ' . $filterOperation . ' :originalReleaseDate')
                ->orderBy('programme.releaseDate', $orderDirection)
                ->setMaxResults(1)
                ->setParameter('parentDbId', $programme->getParent()->getDbId())
                ->setParameter('originalReleaseDate', $programme->getReleaseDate());

            $result = $qb->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);

            if ($result) {
                return $result;
            }
        }

        return null;
    }

    public function findDescendants($programme, $limit, $offset)
    {
        $qb = $this->getChildrenQueryBuilder($programme)
            ->setMaxResults($limit)
            ->setFirstResult($offset);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    private function resolveParents(array $programmes)
    {
        return $this->abstractResolveAncestry(
            $programmes,
            [$this, 'programmeAncestryGetter']
        );
    }

    private function programmeAncestryGetter(array $ids)
    {
        return $this->createQueryBuilder('programme')
            ->addSelect(['image', 'masterBrand', 'network'])
            ->leftJoin('programme.image', 'image')
            ->leftJoin('programme.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->where("programme.id IN(:ids)")
            ->setParameter('ids', $ids)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    private function resolveCategories(array $programmes)
    {
        return $this->abstractResolveNestedAncestry(
            $programmes,
            'categories',
            [$this, 'categoryAncestryGetter']
        );
    }

    private function categoryAncestryGetter(array $ids)
    {
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Category');
        return $repo->findByIds($ids);
    }

    /**
     * A utility for returning the db type for a given Domain object
     * This works as we have symmetry between out Domain Entity and DB Entity
     * names for Programmes
     */
    private function dbType(Programme $entity)
    {
        return substr(strrchr(get_class($entity), '\\'), 1);
    }
}
