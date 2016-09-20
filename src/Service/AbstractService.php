<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use Doctrine\ORM\EntityRepository;

abstract class AbstractService
{
    const DEFAULT_PAGE = 1;
    const DEFAULT_LIMIT = 300;
    const NO_LIMIT = null;

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

    protected function getOffset($limit, $page): int
    {
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
