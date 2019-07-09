<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionsService;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use DateTimeImmutable;
use PHPUnit_Framework_MockObject_MockObject;

class FindActivePromotionsByEntityGroupedByTypeTest extends AbstractPromotionServiceTest
{
    /** @var CoreEntity|PHPUnit_Framework_MockObject_MockObject */
    private $context;

    public function setUp()
    {
        parent::setUp();

        $this->context = $this->createConfiguredMock(CoreEntity::class, [
            'getDbId' => 1,
            'getDbAncestryIds' => [1],
        ]);
    }

    public function testProtocolWithDatabase()
    {
        $this->mockRepository->expects($this->once())
            ->method('findAllActivePromotionsByContext')
            ->with(
                $this->context->getDbAncestryIds(),
                $this->isInstanceOf(DateTimeImmutable::class)
            );

        $this->service()->findAllActivePromotionsByEntityGroupedByType($this->context);
    }

    public function testStructureIsCorrectWhenNoPromotionsResultsFound()
    {
        $this->mockRepository->method('findAllActivePromotionsByContext')->willReturn([]);

        $promotions = $this->service()->findAllActivePromotionsByEntityGroupedByType($this->context);

        $this->assertInternalType('array', $promotions);
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

    public function testStructureIsCorrectWhenFoundPromotionsResults()
    {
        $this->mockRepository->method('findAllActivePromotionsByContext')->willReturn(
            [
                ['pid' => 'p000000h', 'cascadesToDescendants' => false],
                ['pid' => 'p000001h', 'cascadesToDescendants' => true],
            ]
        );

        $promotions = $this->service()->findAllActivePromotionsByEntityGroupedByType($this->context);

        $this->assertInternalType('array', $promotions);
        $this->assertArrayHasKey('regular', $promotions);
        $this->assertArrayHasKey('super', $promotions);
        $this->assertCount(1, $promotions['regular']);
        $this->assertCount(1, $promotions['super']);
        $this->assertEquals('p000000h', $promotions['regular'][0]->getPid());
        $this->assertEquals('p000001h', $promotions['super'][0]->getPid());
    }
}
