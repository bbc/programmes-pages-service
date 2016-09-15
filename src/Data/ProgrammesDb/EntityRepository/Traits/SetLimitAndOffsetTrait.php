<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits;

use BBC\ProgrammesPagesService\Service\AbstractService;
use Doctrine\ORM\QueryBuilder;

trait SetLimitAndOffsetTrait {
    //TODO: See if we should use this throughout pages-service
    public function setLimit(QueryBuilder $qb, int $limit)
    {
        if($limit != AbstractService::NO_LIMIT) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    public function setOffset(QueryBuilder $qb, int $offset)
    {
        $qb->setFirstResult($offset);
        return $qb;
    }
}
