<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChange;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;

class PipsChangeRepository extends EntityRepository
{

    public function addChange(PipsChange $pipsChange)
    {
        $em = $this->getEntityManager();
        $em->persist($pipsChange);
        $em->flush();
    }

    /**
     * @return PipsChange
     */
    public function findLatest()
    {
        try {
            return $this->findOneBy([
                'processedTime' => null,
            ], ['cid' => 'Desc']);
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findLatestByType(array $types = [])
    {
        try {
            return $this->findOneBy([
                'processedTime' => null,
                'entityType' => $types,
            ], ['cid' => 'Desc']);
        } catch (NoResultException $e) {
            return null;
        }
    }

    public function findLatestResults(int $limit = 10, $startCid = null)
    {

        return [];
    }

    public function findLatestResultsByType(array $types = [], int $limit = 10, $startCid = null): array
    {

        return [];
    }

    public function setAsProcessed($changes)
    {

    }
}
