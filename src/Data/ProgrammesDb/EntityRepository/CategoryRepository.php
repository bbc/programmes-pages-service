<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;

class CategoryRepository extends MaterializedPathRepository
{
    use Traits\ParentTreeWalkerTrait;

    private $ancestryCache = [];

    public function findByIds(array $dbIds): array
    {
        $results = $this->createQueryBuilder('category')
            ->andWhere("category.id IN(:ids)")
            ->setParameter('ids', $dbIds)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
        $this->addToAncestryCache($results);
        return $results;
    }

    public function findByUrlKeyAncestryAndType(array $urlKeys, string $type): ?array
    {

        $query = $this->createQueryBuilder('category0')
            ->andWhere('category0 INSTANCE OF :type')
            ->andWhere('category0.urlKey = :urlKey0')
            ->setParameter('type', $type)
            ->setParameter('urlKey0', $urlKeys[0]);

        // Loop through urlKeys, except the first one. Final true value preserves the keys
        foreach (array_slice($urlKeys, 1, null, true) as $i => $urlKey) {
            $query->addSelect('category' . $i)
                ->innerJoin('category' . ($i - 1) . '.parent', 'category' . $i)
                ->andWhere('category' . $i . '.urlKey = :urlKey' . $i)
                ->setParameter('urlKey' . $i, $urlKey);
        }

        // Join onto the what we should be the top category and assert it is not
        // set. Note we need to join to the table here so Doctrine knows that
        // we have attempted to look for the parent and it is null, rather than
        // thinking we haven't attempted to fetch it
        $finalI = count($urlKeys);
        $query->addSelect('category' . $finalI)
            ->leftJoin('category' . ($finalI - 1) . '.parent', 'category' . $finalI)
            ->andWhere('category' . $finalI . '.id IS NULL');

        $result = $query->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        // Get parent categories as array to cache them. Don't laugh.
        $categories = [];
        $category = $result;
        do {
            $categories[] = $category;
        } while (isset($category['parent']) && $category = $category['parent']);
        $this->addToAncestryCache($categories);
        return $result;
    }

    public function findPopulatedChildCategories(
        int $categoryId,
        string $categoryType
    ): array {
        $qb = $this->createQueryBuilder('category')
            ->select(['DISTINCT category'])
            ->join('category.programmes', 'programmes')
            ->andWhere('programmes.parent IS NULL')
            ->andWhere('programmes INSTANCE OF (ProgrammesPagesService:Series, ProgrammesPagesService:Episode, ProgrammesPagesService:Brand)')
            ->andWhere('category.parent = :parentId')
            ->andWhere('category INSTANCE OF :type')
            ->addOrderBy('category.title')
            ->setParameter('parentId', $categoryId)
            ->setParameter('type', $categoryType);

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return $this->resolveParents($result);
    }

    public function findAllByTypeAndMaxDepth(string $type, int $maxDepth): array
    {
        $result = $this->createQueryBuilder('category')
            ->andWhere('category INSTANCE OF :type')
            ->andWhere('category.depth <= :maxDepth')
            ->addOrderBy('category.urlKey')
            ->setParameter('type', $type)
            ->setParameter('maxDepth', $maxDepth)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    public function clearAncestryCache(): void
    {
        $this->ancestryCache = [];
    }

    public function categoryAncestryGetter(array $ids): array
    {
        $cached = [];
        foreach ($ids as $index => $id) {
            if (!isset($this->ancestryCache[$id])) {
                // If any of our ancestors is not in the cache, just do the query
                return $this->findByIds($ids);
            }
            $cached[] = $this->ancestryCache[$id];
        }
        // Return cached ancestors, saving a query
        return $cached;
    }

    protected function resolveParents(array $categories): array
    {
        return $this->abstractResolveAncestry($categories, [$this, 'categoryAncestryGetter']);
    }

    /**
     * This needs to be an array of categories.
     *
     * @param array $results
     */
    private function addToAncestryCache(array $results)
    {
        foreach ($results as $result) {
            if (isset($result['id'])) {
                $this->ancestryCache[$result['id']] = $result;
            }
        }
    }
}
