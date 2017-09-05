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
    public function testReturnResultsFound($dbResults)
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 1]);
        
        $this->mockRepository->method('findByContributionTo')->willReturn($dbResults);

        $contributions = $this->service()->findByContributionToProgramme($programme);

        $this->assertCount(count($dbResults), $contributions);
        $this->assertContainsOnly(Contribution::class, $contributions);
    }

    public function resultsDbProvider()
    {
        return [
            'CASE: found results' => [
                [['pid' => 'b00swyx1'], ['pid' => 'b010t150']]
            ],
            'CASE: not found results' => [[]],
        ];
    }
}
