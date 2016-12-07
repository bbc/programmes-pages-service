<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
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

    protected function getOffset($limit, int $page): int
    {
        if ($limit !== self::NO_LIMIT && !is_integer($limit)) {
            throw new InvalidArgumentException(
                'Limit should either be self::NO_LIMIT or an integer but got ' . $limit
            );
        }

        if ($page < 1) {
            throw new InvalidArgumentException('Page should be greater than 0 but got ' . $page);
        }

        if ($limit === self::NO_LIMIT && $page !== 1) {
            throw new InvalidArgumentException('Page greater than 1 with no limit? Are you sure?');
        }

        return $limit * ($page - 1);
    }

    protected function mapSingleEntity($dbEntity, ...$additionalArgs)
    {
        if (is_null($dbEntity)) {
            return null;
        }


        return $this->mapper->getDomainModel($dbEntity, ...$additionalArgs);
    }

    protected function mapManyEntities(array $dbEntities, ...$additionalArgs): array
    {
        $mappedEntities = [];
        foreach ($dbEntities as $dbEntity) {
            $mappedEntities[] = $this->mapSingleEntity($dbEntity, ...$additionalArgs);
        }
        return $mappedEntities;
    }
}
