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

    public function findByUrlKeyWithDefaultService(string $urlKey): ?Network
    {
        $dbEntity = $this->repository->findByUrlKeyWithDefaultService($urlKey);
        return $this->mapSingleEntity($dbEntity);
    }

    public function findPublishedNetworksByType(
        array $types,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findPublishedNetworksByType(
            $types,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }
}
