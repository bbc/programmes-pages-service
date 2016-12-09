<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use InvalidArgumentException;

class AtozTitleRepository extends EntityRepository
{
    use Traits\SetLimitTrait;
    use Traits\ParentTreeWalkerTrait;

    /**
     * @param string|null $networkMedium
     */
    public function findAllLetters($networkMedium): array
    {
        $qb = $this->createQueryBuilder('AtozTitle')
            ->select(['DISTINCT AtozTitle.firstLetter'])
            ->orderBy('AtozTitle.firstLetter');

        if ($networkMedium) {
            $this->assertNetworkMedium($networkMedium);
            $qb->join('c.masterBrand', 'masterBrand')
                ->join('masterBrand.network', 'network')
                ->andWhere('network.medium = :medium')
                ->setParameter('medium', $networkMedium);
        }

        $result = $qb->getQuery()->getResult(Query::HYDRATE_ARRAY);
        return array_column($result, 'firstLetter');
    }

    public function findTleosByFirstLetter(
        string $letter,
        $networkMedium,
        bool $filterToAvailable,
        $limit,
        int $offset
    ) {
        if (strlen($letter) !== 1) {
            throw new InvalidArgumentException("$letter is not a single letter");
        }
        $letter = strtolower($letter);
        $qb = $this->createQueryBuilder('AtozTitle')
            ->addSelect(['image', 'masterBrand', 'network', 'mbImage'])
            ->leftJoin('c.image', 'image')
            ->leftJoin('c.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->where('AtozTitle.firstLetter = :firstLetter')
            ->andWhere('c INSTANCE OF (ProgrammesPagesService:Brand, ProgrammesPagesService:Series, ProgrammesPagesService:Episode)')
            ->orderBy('AtozTitle.title')
            ->addOrderBy('c.pid')
            ->setFirstResult($offset)
            ->setParameter('firstLetter', $letter);

        if ($filterToAvailable) {
            $qb->andWhere('c.streamable = 1');
        }
        if ($networkMedium) {
            $this->assertNetworkMedium($networkMedium);
            $qb->andWhere('network.medium = :medium');
            $qb->setParameter('medium', $networkMedium);
        }
        $qb = $this->setLimit($qb, $limit);
        $query = $qb->getQuery();
        $result = $query->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    public function countTleosByFirstLetter(
        string $letter,
        $networkMedium,
        bool $filterToAvailable
    ) {
        if (strlen($letter) !== 1) {
            throw new InvalidArgumentException("$letter is not a single letter");
        }
        $letter = strtolower($letter);
        $qb = $this->createQueryBuilder('AtozTitle')
            ->select('count(AtozTitle.id)')
            ->where('AtozTitle.firstLetter = :firstLetter')
            ->andWhere('c INSTANCE OF (ProgrammesPagesService:Brand, ProgrammesPagesService:Series, ProgrammesPagesService:Episode)')
            ->setParameter('firstLetter', $letter);

        if ($filterToAvailable) {
            $qb->andWhere('c.streamable = 1');
        }
        if ($networkMedium) {
            $this->assertNetworkMedium($networkMedium);
            $qb = $qb->join('c.masterBrand', 'masterBrand')
                ->join('masterBrand.network', 'network')
                ->andWhere('network.medium = :medium');
            $qb->setParameter('medium', $networkMedium);
        }
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
    }

    public function createQueryBuilder($alias, $indexBy = null)
    {
        // Any time titles are fetched here they must be inner joined to
        // their programme entity, this allows the embargoed filter to trigger
        // and exclude unwanted items.
        return parent::createQueryBuilder($alias)
            ->addSelect('c')
            ->join($alias . '.coreEntity', 'c');
    }

    private function resolveParents(array $programmes)
    {
        $programmeRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Programme');
        return $this->abstractResolveAncestry(
            $programmes,
            [$programmeRepo, 'findByIds'],
            ['coreEntity', 'ancestry']
        );
    }

    private function assertNetworkMedium(string $medium)
    {
        if (!in_array($medium, [NetworkMediumEnum::TV, NetworkMediumEnum::RADIO])) {
            throw new \InvalidArgumentException('Network medium must be tv or radio');
        }
    }
}
