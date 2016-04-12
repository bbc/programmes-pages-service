<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\PipsChange;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Status;
use Doctrine\ORM\EntityRepository;
use Doctrine\ORM\NoResultException;
use Doctrine\ORM\Query;

class StatusRepository extends EntityRepository
{
    public function getCurrentStatus(): Status
    {
        $status = $this->find(1);
        if (empty($status)) {
            $status = new Status();
            $this->setStatus($status);
        }

        return $status;
    }

    public function setStatus(Status $status)
    {
        $em = $this->getEntityManager();
        $em->persist($status);
        $em->flush();
    }
}
