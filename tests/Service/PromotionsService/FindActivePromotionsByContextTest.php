<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionsService;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use DateTimeImmutable;

class FindActivePromotionsByContextTest extends AbstractPromotionServiceTest
{
    /** @var CoreEntity */
    private $context;

    public function setUp()
    {
        parent::setUp();

        $this->context = $this->createConfiguredMock(CoreEntity::class, [
            'getDbId' => 1,
            'getDbAncestryIds' => [1],
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

        $this->service()->findActivePromotionsByContext($this->context, ...$paramsPagination);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'CASE: default pagination' => [300, 0, []],
            'CASE: custom pagination' => [5, 10, [5, 3]],
        ];
    }

    /**
     * @dataProvider dbPromotionsProvider
     */
    public function testResultsCanBeFetchedAndMapped(array $expectedPids, array $dbPromotionsResults)
    {
        $this->mockRepository->method('findActivePromotionsByContext')->willReturn($dbPromotionsResults);

        $promotions = $this->service()->findActivePromotionsByContext($this->context);

        $this->assertCount(count($expectedPids), $dbPromotionsResults);
        $this->assertContainsOnly(Promotion::class, $promotions);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $promotions[$i]->getPid());
        }
    }

    public function dbPromotionsProvider()
    {
        return [
            'CASE: results found' => [
                ['p000000h'],
                [['pid' => 'p000000h']],
            ],
            'CASE: results no found' => [
                [],
                [],
            ],
        ];
    }
}
