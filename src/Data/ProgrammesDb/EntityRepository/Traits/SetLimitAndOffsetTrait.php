<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits;

use BBC\ProgrammesPagesService\Service\Util\ServiceConstants;
use Doctrine\ORM\QueryBuilder;

trait SetLimitAndOffsetTrait
{

    public function setLimit(QueryBuilder $qb, $limit) : QueryBuilder
    {
        //TODO: See if we should use this throughout pages-service
        if ($limit != ServiceConstants::NO_LIMIT) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }

    public function setOffset(QueryBuilder $qb, $offset) : QueryBuilder
    {
        if ($offset != ServiceConstants::NO_LIMIT) {
            $qb->setFirstResult($offset);
        }
        return $qb;
    }
}
