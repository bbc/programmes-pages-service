<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use Doctrine\ORM\Query;

class CategoryRepository extends MaterializedPathRepository
{
    public function findByIds(array $dbIds)
    {
        return $this->createQueryBuilder('category')
            ->where("category.id IN(:ids)")
            ->setParameter('ids', $dbIds)
            ->getQuery()->getResult(Query::HYDRATE_ARRAY);
    }

    protected function resolveParents(array $categories)
    {
        return $this->abstractResolveAncestry($categories, [$this, 'programmeAncestryGetter']);
    }
}
