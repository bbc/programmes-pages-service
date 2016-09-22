<?php

namespace BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\Traits;

use BBC\ProgrammesPagesService\Service\Util\ServiceConstants;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\Query;
use Doctrine\ORM\QueryBuilder;

/**
 * TODO: See if we should move this to a base class and make the others extend this
 **/
trait SetLimitTrait
{
    /**
     * @param Query|QueryBuilder $qb
     * @param $limit
     * @return mixed
     */
    protected function setLimit($qb, $limit)
    {
        if($limit !== ServiceConstants::NO_LIMIT && !is_integer($limit)) {
            throw new InvalidArgumentException(
                'Limit should either be ServiceConstants::NO_LIMIT or an integer, but got ' . $limit);
        }

        if ($limit !== ServiceConstants::NO_LIMIT) {
            $qb->setMaxResults($limit);
        }

        return $qb;
    }
}
