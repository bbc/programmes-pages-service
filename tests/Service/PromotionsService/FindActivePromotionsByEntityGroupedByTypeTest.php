<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionsService;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use DateTimeImmutable;

class FindActivePromotionsByEntityGroupedByTypeTest extends AbstractPromotionServiceTest
{
    /** @var CoreEntity */
    private $context;

    public function setUp()
    {
        parent::setUp();

        $dbId = 1;
        $context = $this->mockEntity('Brand', $dbId);
        $context->method('getDbAncestryIds')->willReturn([$dbId]);

        $this->context = $context;
    }

    public function testfindActivePromotionsByEntityGroupedByTypeReturnRightStructure()
    {
        $dbData = [['pid' => 'p000000h']];

        $this->mockRepository
            ->method('findActivePromotionsByContext')
            ->with($this->context->getDbAncestryIds(), $this->isInstanceOf(DateTimeImmutable::class), 300, 0)
            ->willReturn($dbData);

        $promotions = $this->service()->findActivePromotionsByEntityGroupedByType($this->context);

        $this->assertEquals(
            [
                'regular' => [],
                'super' => $this->getDomainModelsFromDbData($dbData),
            ],
            $promotions
        );
    }
}
