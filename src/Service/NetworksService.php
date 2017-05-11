<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\NetworkRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\NetworkMapper;

class NetworksService extends AbstractService
{
    public function __construct(
        NetworkRepository $repository,
        NetworkMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByUrlKeyWithDefaultService(string $urlKey, $ttl = CacheInterface::NORMAL): ?Network
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $urlKey, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($urlKey) {
                $dbEntity = $this->repository->findByUrlKeyWithDefaultService($urlKey);
                return $this->mapSingleEntity($dbEntity);
            }
        );
    }

    public function findPublishedNetworksByType(
        array $types,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, implode('|', $types), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($types, $limit, $page) {
                $dbEntities = $this->repository->findPublishedNetworksByType(
                    $types,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
