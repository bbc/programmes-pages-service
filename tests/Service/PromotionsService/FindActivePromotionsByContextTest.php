<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionsService;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use DateTimeImmutable;

class FindActivePromotionsByContextTest extends AbstractPromotionServiceTest
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

    public function testFindActivePromotionsByEntityDefaultPagination()
    {
        $dbData = [['pid' => 'p000000h']];

        $this->mockRepository
            ->method('findActivePromotionsByContext')
            ->with($this->context->getDbAncestryIds(), $this->isInstanceOf(DateTimeImmutable::class), 300, 0)
            ->willReturn($dbData);

        $promotions = $this->service()->findActivePromotionsByContext($this->context);

        $this->assertEquals($this->getDomainModelsFromDbData($dbData), $promotions);
    }

    public function testFindActivePromotionsByEntityCustomPagination()
    {
        $expectedLimit = 30;
        $expectedOffset = 60;
        $dbData = [['pid' => 'p000000h']];

        $this->mockRepository
            ->method('findActivePromotionsByContext')
            ->with($this->context->getDbAncestryIds(), $this->isInstanceOf(DateTimeImmutable::class), $expectedLimit, $expectedOffset)
            ->willReturn($dbData);

        $promotions = $this->service()->findActivePromotionsByContext($this->context, 30, 3);

        $this->assertEquals($this->getDomainModelsFromDbData($dbData), $promotions);
    }

    public function testFindActivePromotionsByEntityNonExistantDbId()
    {
        $dbData = [];

        $this->mockRepository
            ->method('findActivePromotionsByContext')
            ->with($this->context->getDbAncestryIds(), $this->isInstanceOf(DateTimeImmutable::class), 300, 0)
            ->willReturn($dbData);

        $promotions = $this->service()->findActivePromotionsByContext($this->context);

        $this->assertEquals([], $promotions);
    }
}
