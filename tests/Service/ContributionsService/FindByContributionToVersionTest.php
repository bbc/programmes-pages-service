<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindByContributionToVersionTest extends AbstractContributionsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testPagination()
    {
        $version = $this->createConfiguredMock(Version::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$version->getDbId()], 'version', false, 300, 0);

        $this->service()->findByContributionToVersion($version);
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
    public function testResults(array $expectedPids, array $fakeDbContributions)
    {
        $this->mockRepository->method('findByContributionTo')->willReturn($fakeDbContributions);

        $contributions = $this->service()->findByContributionToVersion($this->createMock(Version::class));

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
