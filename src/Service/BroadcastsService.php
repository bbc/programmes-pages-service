<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;
use DateTimeImmutable;
use Psr\Cache\CacheItemPoolInterface;

class BroadcastsService extends AbstractService
{
    /** @var  BroadcastRepository */
    protected $repository;

    public function __construct(
        BroadcastRepository $repository,
        BroadcastMapper $mapper,
        CacheItemPoolInterface $cache
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
        $serviceId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT
    ) {
        $dbEntities = $this->repository->findAllByServiceAndDateRange(
            $serviceId,
            $startDate,
            $endDate,
            $limit
        );

        return $this->mapManyEntities($dbEntities);
    }
}
