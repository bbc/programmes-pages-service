<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;
use DateTimeImmutable;

class BroadcastsService extends AbstractService
{
    /* @var BroadcastMapper */
    protected $mapper;

    /* @var BroadcastRepository */
    protected $repository;

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
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $version->getDbId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($version, $limit, $page) {
                $dbEntities = $this->repository->findByVersion(
                    [$version->getDbId()],
                    'Broadcast',
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function findByServiceAndDateRange(
        Sid $serviceId,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            (string) $serviceId,
            $startDate->getTimestamp(),
            $endDate->getTimestamp(),
            $limit,
            $page,
            $ttl
        );

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($serviceId, $startDate, $endDate, $limit, $page) {
                $dbEntities = $this->repository->findAllByServiceAndDateRange(
                    $serviceId,
                    $startDate,
                    $endDate,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function findOnNowByService(
        Service $service,
        $ttl = CacheInterface::NORMAL
    ): ?Broadcast {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $service->getDbId(),
            $ttl
        );

        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            return $cacheItem->get();
        }

        $dbEntity = $this->repository->findOnNowByService(
            $service->getDbId(),
            'Broadcast',
            ApplicationTime::getTime()
        );

        $entity = $this->mapSingleEntity($dbEntity);

        if ($entity) {
            // cache $entity with dynamic TTL
            $now = ApplicationTime::getTime();
            $diffInSeconds = $entity->getEndAt()->getTimestamp() - $now->getTimestamp();
            if ($diffInSeconds > 300) {
                // limit TTL to 5 minutes max
                $ttl = 300;
            } else {
                $ttl = $diffInSeconds;
            }
            $this->cache->setItem($cacheItem, $entity, $ttl);
        }
        return $entity;
    }

    public function flushOnNowByService(
        Service $service,
        $ttl = CacheInterface::NORMAL
    ): bool {
        $key = $this->cache->keyHelper(
            __CLASS__,
            'findOnNowByService',
            $service->getDbId(),
            $ttl
        );
        return $this->cache->deleteItem($key);
    }
}
