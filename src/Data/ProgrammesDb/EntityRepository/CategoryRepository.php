<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use Doctrine\ORM\Query;

class CategoryRepository extends MaterializedPathRepository
{
    use Traits\ParentTreeWalkerTrait;
    use Traits\NetworkMediumTrait;

    public function findByIds(array $dbIds): array
    {
        return $this->createQueryBuilder('category')
            ->where("category.id IN(:ids)")
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

        $query->andWhere('category' . (count($urlKeys) - 1) . '.parent IS NULL');

        return $query->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
    }

    public function findPopulatedChildCategoriesByNetworkMedium(
        int $categoryId,
        string $categoryType,
        ?string $medium
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

        if ($medium) {
            $this->assertNetworkMedium($medium);
            $qb->join('programmes.masterBrand', 'masterBrand')
                ->join('masterBrand.network', 'network')
                ->andWhere('network.medium = :medium')
                ->setParameter('medium', $medium);
        }

        return $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findUsedByType(string $type): array
    {
        $result = $this->createQueryBuilder('category')
            ->select(['DISTINCT category'])
            ->join('category.programmes', 'programme')
            // When INSTANCE OF receives the type as a parameter, we have to use the actual value that's stored in
            // the db instead of the ProgrammesPagesService:(type) form.
            ->andWhere('category INSTANCE OF :type')
            // We limit the depth because for the pan-bbc feed we don't show more than subcategories
            ->andWhere('category.depth <= 2')
            ->addOrderBy('category.urlKey')
            ->setParameter('type', $type)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    protected function resolveParents(array $categories): array
    {
        return $this->abstractResolveAncestry($categories, [$this, 'findByIds']);
    }
}
