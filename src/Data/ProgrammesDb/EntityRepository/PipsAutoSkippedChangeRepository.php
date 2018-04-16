<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsAutoSkippedChange;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use DateTime;

class PipsAutoSkippedChangeRepository extends EntityRepository
{
    public function addSkippedEntity(PipsAutoSkippedChange $pipsSkippedEntity)
    {
        $em = $this->getEntityManager();
        $em->persist($pipsSkippedEntity);
        $em->flush();
    }

    /**
     * @param mixed $cid
     * @return null|PipsAutoSkippedChange
     */
    public function findById($cid)
    {
        return $this->find($cid);
    }
}
