<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;

class FindByContributionToSegmentTest extends AbstractContributionsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testPagination(int $expectedLimit, int $expectedOffset, array $paginationParams)
    {
        $segment = $this->createConfiguredMock(Segment::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$segment->getDbId()], 'segment', false, $expectedLimit, $expectedOffset);

        $this->service()->findByContributionToSegment($segment, ...$paginationParams);
    }

    public function paginationProvider(): array
    {
        return [
            // expected limit, expected offset, user pagination params
            'CASE: default pagination' => [300, 0, []],
            'CASE: custom pagination' => [3, 12, [3, 5]],
        ];
    }

    /**
     * @dataProvider dbContributionsProvider
     */
    public function testsResults(array $expectedPids, array $fakeDbContributions)
    {
        $this->mockRepository->method('findByContributionTo')->willReturn($fakeDbContributions);

        $contributions = $this->service()->findByContributionToSegment($this->createMock(Segment::class));

        $this->assertCount(count($fakeDbContributions), $contributions);
        $this->assertContainsOnly(Contribution::class, $contributions);
        $this->assertEquals($expectedPids, $this->extractPids($contributions));
    }

    public function dbContributionsProvider(): array
    {
        return [
            'CASE: found results' => [
                ['b00swyx1', 'b010t150'],
                [['pid' => 'b00swyx1'], ['pid' => 'b010t150']],
            ],
            'CASE: not found results' => [[], []],
        ];
    }
}
