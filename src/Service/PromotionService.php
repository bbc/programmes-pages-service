<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\PromotionMapper;
use DateTimeImmutable;

class PromotionService extends AbstractService
{
    /** @var PromotionRepository */
    protected $repository;

    public function __construct(
        PromotionRepository $repository,
        PromotionMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    /**
     * @return Promotion[]
     * @throws DataNotFetchedException if we get a promotion that is promoting
     *     nothing or we don't get ny data about what promoting.
     */
    public function findActivePromotionsByProgramme(
        Programme $programme,
        DateTimeImmutable $dateTime,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getPid(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $dateTime, $limit, $page) {
                $dbEntities = $this->repository->findActivePromotionsByPid(
                    $programme->getPid(),
                    $dateTime,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    /**
     * @return Promotion[]
     * @throws DataNotFetchedException if we get a promotion that is
     *     promoting nothing or we don't get any data about what promoting.
     */
    public function findActiveSuperPromotionsByProgramme(
        Programme $programme,
        DateTimeImmutable $dateTime,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__,  $programme->getPid(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $dateTime, $limit, $page) {
                $dbEntities = $this->repository->findActiveSuperPromotionsByAncestry(
                    $programme->getDbAncestryIds(),
                    $dateTime,
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }
}
