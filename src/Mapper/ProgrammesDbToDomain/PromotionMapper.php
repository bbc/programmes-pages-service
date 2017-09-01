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

    private $cache = [];

    /**
     * @param mixed[] $dbPromotion
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
     * @param mixed[] $dbPromotion
     */
    private function getCacheKeyForPromotion(array $dbPromotion): string
    {
        return $this->buildCacheKey($dbPromotion, 'id', [
            'promotionOfCoreEntity' => 'CoreEntity',
            'promotionOfImage' => 'Image',
        ], [
            'relatedLinks' => 'RelatedLink',
        ]);
    }

    /**
     * @param mixed[] $dbPromotion
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
            $dbPromotion['cascadesToDescendants'],
            $this->getRelatedLinksModels($dbPromotion, 'relatedLinks')
        );
    }

    /**
     * @param mixed[] $dbPromotion
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

    /**
     * @param mixed[] $dbPromotion
     * @param string $key
     * @return RelatedLink[]|null
     */
    private function getRelatedLinksModels(array $dbPromotion, string $key = 'relatedLinks'): ?array
    {
        if (!isset($dbPromotion[$key]) || !is_array($dbPromotion[$key])) {
            return null;
        }

        $relatedLinkMapper = $this->mapperFactory->getRelatedLinkMapper();
        $relatedLinks = [];
        foreach ($dbPromotion[$key] as $dbRelatedLink) {
            $relatedLinks[] = $relatedLinkMapper->getDomainModel($dbRelatedLink);
        }

        return $relatedLinks;
    }
}
