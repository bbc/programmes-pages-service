<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository;
use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\PromotionMapper;

class PromotionsService extends AbstractService
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
     * @param CoreEntity $context
     * @param string $ttl
     * @param string $nullTtl
     * @return Promotion[]
     */
    public function findAllActivePromotionsByContext(
        CoreEntity $context,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $context->getDbId(), $ttl, $nullTtl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($context) {
                $dbEntities = $this->repository->findAllActivePromotionsByContext(
                    $context->getDbAncestryIds(),
                    ApplicationTime::getTime()
                );

                return $this->mapManyEntities($dbEntities);
            },
            [],
            $nullTtl
        );
    }

    public function findActiveNonSuperPromotionsByContext(
        CoreEntity $context,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $context->getDbId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($context, $limit, $page) {
                $dbEntities = $this->repository->findActiveNonSuperPromotionsByContext(
                    $context->getDbId(),
                    ApplicationTime::getTime(),
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            },
            [],
            $nullTtl
        );
    }

    public function findAllActivePromotionsByEntityGroupedByType(
        CoreEntity $context,
        $ttl = CacheInterface::NORMAL
    ): array {
        $promotions = $this->findAllActivePromotionsByContext($context, $ttl);

        $groupedPromotions = [
            'regular' => [],
            'super' => [],
        ];

        foreach ($promotions as $promotion) {
            if ($promotion->isSuperPromotion()) {
                $groupedPromotions['super'][] = $promotion;
            } else {
                $groupedPromotions['regular'][] = $promotion;
            }
        }

        return $groupedPromotions;
    }
}
