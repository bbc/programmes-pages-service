<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Gedmo\Tree\Entity\Repository\MaterializedPathRepository;
use Doctrine\ORM\Query;
use Doctrine\ORM\Query\Expr\Join;

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

    public function findByUrlKeyAncestryAndType(
        string $type,
        string $urlKey1,
        string $urlKey2 = null,
        string $urlKey3 = null
    ) {
        $query = $this->createQueryBuilder('category')
            ->andWhere('category.parent IS NULL')
            ->andWhere('category.urlKey = :urlKey1')
            ->andWhere('category INSTANCE OF :type')
            ->setParameter('type', $type)
            ->setParameter('urlKey1', $urlKey1);

        if ($urlKey2) {
            $query->select('subcategory1')
                ->innerJoin($this->_entityName, 'subcategory1', Join::WITH, 'subcategory1.parent = category')
                ->andWhere('subcategory1.urlKey = :urlKey2')
                ->setParameter('urlKey2', $urlKey2);
        }

        if ($urlKey3) {
            $query->select('subcategory2')
                ->innerJoin($this->_entityName, 'subcategory2', Join::WITH, 'subcategory2.parent = subcategory1')
                ->andWhere('subcategory2.urlKey = :urlKey3')
                ->setParameter('urlKey3', $urlKey3);
            ;
        }

        $result = $query->getQuery()->getOneOrNullResult(Query::HYDRATE_ARRAY);
        $resolvedParentage = $this->resolveParents([$result]);

        return reset($resolvedParentage);
    }

    public function findChildCategoriesUsedByTleosByParentIdAndType(string $categoryId, string $categoryType)
    {
        $r = $this->createQueryBuilder('category')
            ->select(['DISTINCT category'])
            ->join('category.programmes', 'programmes')
            ->andWhere('programmes.parent IS NULL')
            ->andWhere('category.parent = :parentId')
            ->andWhere('category INSTANCE OF :type')
            ->addOrderBy('category.title')
            ->setParameter('parentId', $categoryId)
            ->setParameter('type', $categoryType);
        $res = $r->getQuery()->getResult(Query::HYDRATE_ARRAY);
        var_dump($res);

        return $res;
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
