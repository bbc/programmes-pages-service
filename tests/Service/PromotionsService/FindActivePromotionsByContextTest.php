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
        $context = $this->mockEntity('CoreEntity', $dbId);
        $context->method('getDbAncestryIds')->willReturn([$dbId]);

        $this->context = $context;
    }

    public function testFindActivePromotionsByEntityDefaultPagination()
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findActivePromotionsByContext')
            ->with($this->context->getDbAncestryIds(), $this->isInstanceOf(DateTimeImmutable::class), 300, 0);

        $this->service()->findActivePromotionsByContext($this->context);
    }

    public function testFindActivePromotionsByEntityCustomPagination()
    {
        $this->mockRepository
            ->expects($this->once())
            ->method('findActivePromotionsByContext')
            ->with($this->context->getDbAncestryIds(), $this->isInstanceOf(DateTimeImmutable::class), 30, 60);

        $this->service()->findActivePromotionsByContext($this->context, 30, 3);
    }

    public function testFindActivePromotionsByEntityNonExistantDbId()
    {
        $dbData = [];

        $this->mockRepository
            ->method('findActivePromotionsByContext')
            ->willReturn($dbData);

        $promotions = $this->service()->findActivePromotionsByContext($this->context);

        $this->assertEquals([], $promotions);
    }

    public function testFindActivePromotionsByEntityReturnCorrectData()
    {
        $dbData = [['pid' => 'p000000h']];

        $this->mockRepository
            ->method('findActivePromotionsByContext')
            ->willReturn($dbData);

        $promotions = $this->service()->findActivePromotionsByContext($this->context);

        $this->assertEquals($this->getDomainModelsFromDbData($dbData), $promotions);
    }

}
