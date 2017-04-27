<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use Doctrine\Common\Proxy\Exception\InvalidArgumentException;
use Doctrine\ORM\EntityRepository;
use Psr\Cache\CacheItemPoolInterface;

abstract class AbstractService
{
    public const DEFAULT_PAGE = 1;
    public const DEFAULT_LIMIT = 300;
    public const NO_LIMIT = null;

    /**
     * @var CacheItemPoolInterface
     */
    private $cache;

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
        MapperInterface $mapper,
        CacheItemPoolInterface $cache
    ) {
        $this->repository = $repository;
        $this->mapper = $mapper;
        $this->cache = $cache;
    }

    protected function getOffset(?int $limit, int $page): int
    {
        if ($page < 1) {
            throw new InvalidArgumentException('Page should be greater than 0 but got ' . $page);
        }

        if ($limit === self::NO_LIMIT && $page !== 1) {
            throw new InvalidArgumentException('Page greater than 1 with no limit? Are you sure?');
        }

        return $limit * ($page - 1);
    }

    protected function mapSingleEntity(?array $dbEntity, ...$additionalArgs)
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
