<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Segment;

class FindByContributionToSegmentsTest extends AbstractContributionsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testProtocolWithDatabase(int $expectedLimit, int $expectedOffset, array $paginationParams)
    {
        $segments = [
            $this->createConfiguredMock(Segment::class, ['getDbId' => 111]),
            $this->createConfiguredMock(Segment::class, ['getDbId' => 222]),
            $this->createConfiguredMock(Segment::class, ['getDbId' => 333]),
        ];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([111, 222, 333], 'segment', true, $expectedLimit, $expectedOffset);

        $this->service()->findByContributionToSegments($segments, ...$paginationParams);
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
    public function testResultsFromService(array $expectedPids, array $fakeDbContributions)
    {
        $segments = [
            $this->createMock(Segment::class),
            $this->createMock(Segment::class),
            $this->createMock(Segment::class),
        ];

        $this->mockRepository->method('findByContributionTo')->willReturn($fakeDbContributions);
        $contributions = $this->service()->findByContributionToSegments($segments);

        $this->assertCount(count($fakeDbContributions), $contributions);
        $this->assertContainsOnly(Contribution::class, $contributions);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $contributions[$i]->getPid());
        }
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
