<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\PromotionsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractPromotionServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('PromotionRepository');
        // override getDomainModel() in mapper with getDomainModelFromDbData()
        $this->setUpMapper('PromotionMapper', 'getDomainModelFromDbData');
    }

    /**
     * @param mixed[] $dbPromotion
     */
    protected function getDomainModelFromDbData(array $dbPromotion): Promotion
    {
        $pid = new Pid($dbPromotion['pid']);

        $mockPromotion = $this->createMock(self::ENTITY_NS . 'Promotion');
        $mockPromotion->method('getPid')->willReturn($pid);
        if (isset($dbPromotion['cascadesToDescendants'])) {
            $mockPromotion->method('isSuperPromotion')
              ->will($this->returnValue($dbPromotion['cascadesToDescendants']));
        }

        return $mockPromotion;
    }

    /**
     * @param array[] $dbPromotions
     * @return Promotion[]
     */
    protected function getDomainModelsFromDbData(array $dbPromotions): array
    {
        return array_map([$this, 'getDomainModelFromDbData'], $dbPromotions);
    }

    protected function service(): PromotionsService
    {
        return new PromotionsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
