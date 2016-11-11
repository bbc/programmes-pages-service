<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use Doctrine\ORM\Query;

class CategoryRepository extends MaterializedPathRepository
{
    use Traits\ParentTreeWalkerTrait;

    public function findByIds(array $dbIds)
    {
        return $this->createQueryBuilder('category')
            ->where("category.id IN(:ids)")
            ->setParameter('ids', $dbIds)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    public function findUsedByType(string $type)
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

    protected function resolveParents(array $categories)
    {
        return $this->abstractResolveAncestry($categories, [$this, 'findByIds']);
    }
}
