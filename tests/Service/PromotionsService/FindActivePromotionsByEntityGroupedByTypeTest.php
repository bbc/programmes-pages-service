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

        $this->context = $this->createConfiguredMock(CoreEntity::class, [
            'getDbId' => 1,
            'getDbAncestryIds' => [1]
        ]);
    }

    /**
     * @dataProvider paginationProvider
     */
    public function testProtocolWithDatabase(int $expectedLimit, int $expectedOffset, array $paramsPagination)
    {
        $this->mockRepository->expects($this->once())
            ->method('findActivePromotionsByContext')
            ->with(
                $this->context->getDbAncestryIds(),
                $this->isInstanceOf(DateTimeImmutable::class),
                $expectedLimit,
                $expectedOffset
            );

        $this->service()->findActivePromotionsByEntityGroupedByType($this->context, ...$paramsPagination);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'CASE: default pagination' => [300, 0, []],
            'CASE: custom pagination' => [5, 10, [5, 3]],
        ];
    }

    public function testStructureIsCorrectWhenNoPromotionsResultsFound()
    {
        $this->mockRepository->method('findActivePromotionsByContext')->willReturn([]);

        $promotions = $this->service()->findActivePromotionsByEntityGroupedByType($this->context);

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
        $this->mockRepository->method('findActivePromotionsByContext')->willReturn(
            [
                ['pid' => 'p000000h', 'cascadesToDescendants' => false],
                ['pid' => 'p000001h', 'cascadesToDescendants' => true],
            ]
        );

        $promotions = $this->service()->findActivePromotionsByEntityGroupedByType($this->context);

        $this->assertInternalType('array', $promotions);
        $this->assertArrayHasKey('regular', $promotions);
        $this->assertArrayHasKey('super', $promotions);
        $this->assertCount(1, $promotions['regular']);
        $this->assertCount(1, $promotions['super']);
        $this->assertEquals('p000000h', $promotions['regular'][0]->getPid());
        $this->assertEquals('p000001h', $promotions['super'][0]->getPid());
    }
}
