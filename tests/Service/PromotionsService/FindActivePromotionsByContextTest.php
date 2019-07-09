<?php

namespace Tests\BBC\ProgrammesPagesService\Service\PromotionsService;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\Entity\Promotion;
use DateTimeImmutable;
use PHPUnit_Framework_MockObject_MockObject;

class FindActivePromotionsByContextTest extends AbstractPromotionServiceTest
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

        $this->service()->findAllActivePromotionsByContext($this->context);
    }

    /**
     * @dataProvider dbPromotionsProvider
     */
    public function testResultsCanBeFetchedAndMapped(array $expectedPids, array $dbPromotionsResults)
    {
        $this->mockRepository->method('findAllActivePromotionsByContext')->willReturn($dbPromotionsResults);

        $promotions = $this->service()->findAllActivePromotionsByContext($this->context);

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
