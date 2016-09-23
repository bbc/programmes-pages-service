<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Service\Util\ServiceConstants;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;

abstract class AbstractService
{
    /**
     * @var MapperInterface
     */
    protected $mapper;

    /**
     * @var EntityRepository
     */
    protected $repository;

    public function __construct(
        EntityRepository $repository,
        MapperInterface $mapper
    ) {
        $this->repository = $repository;
        $this->mapper = $mapper;
    }

    protected function getOffset($limit, int $page): int
    {
        if ($limit !== ServiceConstants::NO_LIMIT && !is_integer($limit)) {
            throw new InvalidArgumentException(
                'Limit should either be ServiceConstants::NO_LIMIT or an integer but got ' . $limit
            );
        }

        if ($page < 1) {
            throw new InvalidArgumentException('Page should be greater than 0 but got ' . $page);
        }

        if ($limit === ServiceConstants::NO_LIMIT && $page !== 1) {
            throw new InvalidArgumentException('Page greater than 1 with no limit? Are you sure?');
        }

        return $limit * ($page - 1);
    }

    protected function mapSingleEntity($dbEntity)
    {
        if (is_null($dbEntity)) {
            return null;
        }

        return $this->mapper->getDomainModel($dbEntity);
    }

    protected function mapManyEntities(array $dbEntities): array
    {
        return array_map([$this, 'mapSingleEntity'], $dbEntities);
    }
}
