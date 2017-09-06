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
        // override getDomainModel() in mapper
        $this->setUpMapper('PromotionMapper', function($dbPromotion) {
            return $this->createConfiguredMock(Promotion::class, [
                'getPid' => new Pid($dbPromotion['pid']),
                'isSuperPromotion' => $dbPromotion['cascadesToDescendants'] ?? false
            ]);
        });
    }

    protected function service(): PromotionsService
    {
        return new PromotionsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
