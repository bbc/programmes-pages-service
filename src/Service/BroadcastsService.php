<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;
use DateTimeImmutable;
use BBC\ProgrammesPagesService\Cache\CacheInterface;

class BroadcastsService extends AbstractService
{
    public function __construct(
        BroadcastRepository $repository,
        BroadcastMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByVersion(
        Version $version,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByVersion(
            [$version->getDbId()],
            'Broadcast',
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findByServiceAndDateRange(
        Sid $serviceId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findAllByServiceAndDateRange(
            $serviceId,
            $startDate,
            $endDate,
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }
}
