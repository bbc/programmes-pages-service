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
            'CASE: default' => [300, 0, []],
            'CASE: custom' => [3, 12, [3, 5]],
        ];
    }

    /**
     * @dataProvider resultsDbProvider
     */
    public function testFindByContributionToSegmentCustomPagination($fakeDbContributions)
    {
        $this->mockRepository->method('findByContributionTo')->willReturn($fakeDbContributions);

        $contributions = $this->service()->findByContributionToSegment($this->createMock(Segment::class));

        $this->assertCount(count($fakeDbContributions), $fakeDbContributions);
        $this->assertContainsOnly(Contribution::class, $contributions);
    }

    public function resultsDbProvider(): array
    {
        return [
            'CASE: found results' => [
                [['pid' => 'b00swyx1'], ['pid' => 'b010t150']]
            ],
            'CASE: not found results' => [[]],
        ];
    }
}
