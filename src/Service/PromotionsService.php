<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
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
     * @return Promotion[]
     */
    public function findActivePromotionsByContext(
        CoreEntity $context,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $context->getDbId(), $limit, $page, $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($context, $limit, $page) {
                $dbEntities = $this->repository->findActivePromotionsByContext(
                    $context->getDbAncestryIds(),
                    ApplicationTime::getTime(),
                    $limit,
                    $this->getOffset($limit, $page)
                );

                return $this->mapManyEntities($dbEntities);
            }
        );
    }

    public function findActivePromotionsByEntityGroupedByType(
        CoreEntity $context,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $promotions = $this->findActivePromotionsByContext($context, $limit, $page, $ttl);

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
