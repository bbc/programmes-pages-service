<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\PromotionService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractPromotionServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('PromotionRepository');
        $this->setUpMapper('PromotionMapper', 'overrideGetDomainModel');
    }

    /**
     * override getDomainModel for each db promotion to produce a mocked promotion
     */
    protected function overrideGetDomainModel($dbpromotion)
    {
        $pid = new Pid($dbpromotion['pid']);

        $mockPromotion = $this->createMock(self::ENTITY_NS . 'Promotion');
        $mockPromotion->method('getPid')->willReturn($pid);

        return $mockPromotion;
    }

    protected function getDomainModelsFromDbData(array $dbPromotions)
    {
        return array_map([$this, 'overrideGetDomainModel'], $dbPromotions);
    }

    protected function service(): PromotionService
    {
        return new PromotionService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    protected function stubDbData()
    {
        return [
            [
                'id' => 1,
                'pid' => 'p000000h',
                'weighting' => 73,
                'title' => 'active promotion of CoreEntity',
                'uri' => 'www.myuri.com',
                'shortSynopsis' => 'a short synopsis',
                'mediumSynopsis' => 'a medium synopsis',
                'longSynopsis' => 'a long synopsys',
                'promotionOfCoreEntity' => null,
                'promotionOfImage' => [
                    'key' => 'index',
                ],
            ],
        ];
    }

    protected function getMockProgramme()
    {
        $coreEntity = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Brand');
        $coreEntity->method('getPid')->willReturn(new Pid('b00101ccdd'));
        $coreEntity->method('getDbAncestryIds')->willReturn(['12,146']);
        return $coreEntity;
    }
}
