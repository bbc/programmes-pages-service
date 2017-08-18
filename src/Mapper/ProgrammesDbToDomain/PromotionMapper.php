<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\PromotableInterface;
use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\Traits\SynopsesTrait;

class PromotionMapper extends AbstractMapper
{
    use SynopsesTrait;

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
            $dbPromotion['weighting'],
            $dbPromotion['cascadesToDescendants']
        );
    }

    /**
     * @param array[] $dbPromotion
     * @throws DataNotFetchedException if we get a promotion but there is nothing being
     *     promoted (an image or core entity) associated to the promotion
     */
    private function getPromotableModel(array $dbPromotion): PromotableInterface
    {
        if (isset($dbPromotion['promotionOfCoreEntity'])) {
            return $this->mapperFactory
                ->getCoreEntityMapper()
                ->getDomainModel($dbPromotion['promotionOfCoreEntity']);
        }

        if (isset($dbPromotion['promotionOfImage'])) {
            return $this->mapperFactory
                ->getImageMapper()
                ->getDomainModel($dbPromotion['promotionOfImage']);
        }

        throw new DataNotFetchedException('All promotions must be joined to CoreEntity and Image');
    }
}
