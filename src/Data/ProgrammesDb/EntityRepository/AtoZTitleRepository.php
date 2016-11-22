<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Domain\Enumeration\NetworkMediumEnum;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\Query;
use InvalidArgumentException;

class AtoZTitleRepository extends EntityRepository
{
    use Traits\SetLimitTrait;
    use Traits\ParentTreeWalkerTrait;

    public function findAllLetters()
    {
        $query = $this->createQueryBuilder('AtoZTitle')
            ->select(['DISTINCT AtoZTitle.firstLetter'])
            ->orderBy('AtoZTitle.firstLetter')
            ->getQuery();

        $results = $query->getResult(Query::HYDRATE_ARRAY);
        $letters = [];
        foreach ($results as $result) {
            $letters[] = $result['firstLetter'];
        }
        return $letters;
    }

    public function findTLEOsByFirstLetter(
        string $letter,
        $limit,
        int $offset,
        string $networkUrlKey = null,
        bool $filterToAvailable = false
    ) {
        if (strlen($letter) !== 1) {
            throw new InvalidArgumentException("$letter is not a single letter");
        }
        $qb = $this->createQueryBuilder('AtoZTitle')
            ->select(['AtoZTitle', 'c', 'image', 'masterBrand', 'network', 'mbImage'])
            ->leftJoin('c.image', 'image')
            ->leftJoin('c.masterBrand', 'masterBrand')
            ->leftJoin('masterBrand.network', 'network')
            ->leftJoin('masterBrand.image', 'mbImage')
            ->where('AtoZTitle.firstLetter = :firstLetter')
            ->andWhere('c INSTANCE OF ProgrammesPagesService:Brand OR c INSTANCE OF ProgrammesPagesService:Series OR c INSTANCE OF ProgrammesPagesService:Episode')
            ->orderBy('AtoZTitle.title')
            ->addOrderBy('c.pid')
            ->setFirstResult($offset)
            ->setParameter('firstLetter', $letter);

        if ($filterToAvailable) {
            $qb->andWhere('c.streamable = 1');
        }
        if ($networkUrlKey) {
            if (in_array($networkUrlKey, [NetworkMediumEnum::RADIO, NetworkMediumEnum::TV])) {
                $qb->andWhere('network.medium = :medium');
                $qb->setParameter('medium', $networkUrlKey);
            } else {
                $qb->andWhere('network.urlKey = :urlKey');
                $qb->setParameter('urlKey', $networkUrlKey);
            }
        }
        $qb = $this->setLimit($qb, $limit);
        $query = $qb->getQuery();
        $result = $query->getResult(Query::HYDRATE_ARRAY);

        return $this->resolveParents($result);
    }

    public function countTLEOsByFirstLetter(string $letter, string $networkUrlKey = null, bool $filterToAvailable = false)
    {
        if (strlen($letter) !== 1) {
            throw new InvalidArgumentException("$letter is not a single letter");
        }
        $qb = $this->createQueryBuilder('AtoZTitle')
            ->select('count(AtoZTitle.id)')
            ->where('AtoZTitle.firstLetter = :firstLetter')
            ->andWhere('c INSTANCE OF ProgrammesPagesService:Brand OR c INSTANCE OF ProgrammesPagesService:Series OR c INSTANCE OF ProgrammesPagesService:Episode')
            ->setParameter('firstLetter', $letter);

        if ($filterToAvailable) {
            $qb->andWhere('c.streamable = 1');
        }
        if ($networkUrlKey) {
            if (in_array($networkUrlKey, [NetworkMediumEnum::RADIO, NetworkMediumEnum::TV])) {
                $qb->andWhere('network.medium = :medium');
                $qb->setParameter('medium', $networkUrlKey);
            } else {
                $qb->andWhere('network.urlKey = :urlKey');
                $qb->setParameter('urlKey', $networkUrlKey);
            }
        }
        $query = $qb->getQuery();
        return $query->getSingleScalarResult();
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

    public function createQueryBuilder($alias, $indexBy = null)
    {
        // Any time titles are fetched here they must be inner joined to
        // their programme entity, this allows the embargoed filter to trigger
        // and exclude unwanted items.
        return parent::createQueryBuilder($alias)
            ->addSelect('c')
            ->join($alias . '.coreEntity', 'c');
    }
}
