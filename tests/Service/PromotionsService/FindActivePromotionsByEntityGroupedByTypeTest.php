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
        $context = $this->mockEntity('CoreEntity', $dbId);
        $context->method('getDbAncestryIds')->willReturn([$dbId]);

        $this->context = $context;
    }

    public function testfindActivePromotionsByEntityGroupedByTypeReturnRightStructureWithDefaultPagination()
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findActivePromotionsByContext')
            ->with($this->context->getDbAncestryIds(), $this->isInstanceOf(DateTimeImmutable::class), 300, 0);

        $this->service()->findActivePromotionsByEntityGroupedByType($this->context);
    }

    public function testfindActivePromotionsByEntityGroupedByTypeReturnRightStructureWithCustomPagination()
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findActivePromotionsByContext')
            ->with($this->context->getDbAncestryIds(), $this->isInstanceOf(DateTimeImmutable::class), 5, 145);

        $this->service()->findActivePromotionsByEntityGroupedByType($this->context, 5, 30);
    }

    public function testfindActivePromotionsByEntityGroupedByTypeReturnEmptyStructure()
    {
        $dbData = [];

        $this->mockRepository
            ->method('findActivePromotionsByContext')
            ->willReturn($dbData);

        $promotions = $this->service()->findActivePromotionsByEntityGroupedByType($this->context);

        $this->assertInternalType('array',$promotions);
        $this->assertArrayHasKey('regular', $promotions);
        $this->assertArrayHasKey('super', $promotions);

        $this->assertEquals(
            [
                'regular' => [],
                'super' => [],
            ],
            $promotions
        );
    }

    public function testfindActivePromotionsByEntityGroupedByTypeReturnRightStructure()
    {
        $dbData = [
            [
                'pid' => 'p000000h',
                'cascadesToDescendants' => false,
            ], [
                'pid' => 'p000001h',
                'cascadesToDescendants' => true,
            ],
        ];

        $this->mockRepository
            ->method('findActivePromotionsByContext')
            ->willReturn($dbData);

        $promotions = $this->service()->findActivePromotionsByEntityGroupedByType($this->context);

        $this->assertInternalType('array',$promotions);
        $this->assertArrayHasKey('regular', $promotions);
        $this->assertArrayHasKey('super', $promotions);

        $this->assertCount(1, $promotions['regular']);
        $this->assertCount(1, $promotions['super']);

        $this->assertEquals('p000000h', (string) $promotions['regular'][0]->getPid());
        $this->assertEquals('p000001h', (string) $promotions['super'][0]->getPid());
    }
}
