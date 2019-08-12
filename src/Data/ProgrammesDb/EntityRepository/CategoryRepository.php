<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Doctrine\ORM\Query;
use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;

class CategoryRepository extends MaterializedPathRepository
{
    use Traits\ParentTreeWalkerTrait;

    public function findByIds(array $dbIds): array
    {
        return $this->createQueryBuilder('category')
            ->andWhere("category.id IN(:ids)")
            ->setParameter('ids', $dbIds)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
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
            ->andWhere('category' . $finalI . '.id IS NULL')
            ->setMaxResults(1);

        return $query->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findAllDescendantsByParentId(
        int $parentId,
        string $categoryType
    ): array {
        $qb = $this->createQueryBuilder('category')
            ->select(['category'])
            ->addSelect('children')
            ->leftJoin('category.children', 'children')
            ->andWhere('category.parent = :parentid')
            ->andWhere('category INSTANCE OF :type')
            ->addOrderBy('category.title')
            ->setParameter('parentid', $parentId)
            ->setParameter('type', $categoryType);

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
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

    public function findTopLevelGenresAndChildren(): array
    {
        $result = $this->createQueryBuilder('category')
            ->addSelect('children')
            ->leftJoin('category.children', 'children')
            ->andWhere('category INSTANCE OF ProgrammesPagesService:Genre')
            ->andWhere('category.depth = 1')
            ->addOrderBy('category.urlKey')
            ->addOrderBy('children.urlKey')
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    protected function resolveParents(array $categories): array
    {
        return $this->abstractResolveAncestry($categories, [$this, 'findByIds']);
    }
}
