<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindByContributionToProgrammeTest extends AbstractContributionsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testPagination($expectedLimit, $expectedOffset, array $paginationParams)
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$programme->getDbId()], 'programme', false, $expectedLimit, $expectedOffset);

        $this->service()->findByContributionToProgramme($programme, ...$paginationParams);
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
    public function testReturnResultsFound(array $expectedPids, array $fakeDbContributions)
    {
        $this->mockRepository->method('findByContributionTo')->willReturn($fakeDbContributions);

        $contributions = $this->service()->findByContributionToProgramme($this->createMock(Programme::class));

        $this->assertCount(count($fakeDbContributions), $contributions);
        $this->assertContainsOnly(Contribution::class, $contributions);
        $this->assertEquals($expectedPids, $this->extractPids($contributions));
    }

    public function resultsDbProvider(): array
    {
        return [
            'CASE: found results' => [
                ['b00swyx1', 'b010t150'],
                [['pid' => 'b00swyx1'], ['pid' => 'b010t150']]
            ],
            'CASE: not found results' => [[], []],
        ];
    }
}
