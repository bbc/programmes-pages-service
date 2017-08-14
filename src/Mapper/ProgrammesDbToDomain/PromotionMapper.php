<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\PromotableInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Domain\ValueObject\Synopses;
use InvalidArgumentException;

class PromotionMapper extends AbstractMapper
{
    /**
     * @param array[] $dbPromotion
     */
    public function getDomainModel(array $dbPromotion): Promotion
    {
        $cacheKey = $this->getCacheKeyForPromotion($dbPromotion);
        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = $this->getModelForPromotion($dbPromotion);
        }

        return $this->cache[$cacheKey];
    }

    /**
     * @param array[] $dbPromotion
     */
    private function getCacheKeyForPromotion(array $dbPromotion): string
    {
        return $this->buildCacheKey(
            $dbPromotion,
            'id',
            [
                'promotionOfCoreEntity' => 'CoreEntity',
                'promotionOfImage' => 'Image',
            ]
        );
    }

    /**
     * @param array[] $dbPromotion
     */
    private function getModelForPromotion(array $dbPromotion): Promotion
    {
        return new Promotion(
            new Pid($dbPromotion['pid']),
            $this->getPromotableModel($dbPromotion),
            $dbPromotion['title'],
            $this->getSynopses($dbPromotion),
            $dbPromotion['uri'],
            $dbPromotion['weighting']
        );
    }

    /**
     * @param array[] $dbPromotion
     */
    private function getSynopses(array $dbPromotion): Synopses
    {
        return new Synopses(
            $dbPromotion['shortSynopsis'],
            $dbPromotion['mediumSynopsis'],
            $dbPromotion['longSynopsis']
        );
    }

    /**
     * @param array[] $dbPromotion
     */
    private function getPromotableModel(array $dbPromotion): PromotableInterface
    {
        if (!empty($dbPromotion['promotionOfCoreEntity'])) {
            return $this->mapperFactory
                ->getCoreEntityMapper()
                ->getDomainModel($dbPromotion['promotionOfCoreEntity']);
        }

        if (!empty($dbPromotion['promotionOfImage'])) {
            return $this->mapperFactory
                ->getImageMapper()
                ->getDomainModel($dbPromotion['promotionOfImage']);
        }

        throw new InvalidArgumentException('A promotion has to promote something, but this one is promoting nothing');
    }
}
