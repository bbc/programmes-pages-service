<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindByContributionToProgrammeTest extends AbstractContributionsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testPagination(int $expectedLimit, int $expectedOffset, array $paginationParams)
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
            // expected limit, expected offset, user pagination params
          'CASE: default pagination' => [300, 0, []],
          'CASE: custom pagination' => [3, 12, [3, 5]],
        ];
    }

    /**
     * @dataProvider dbContributionsProvider
     */
    public function testReturnResultsFound(array $expectedPids, array $fakeDbContributions)
    {
        $this->mockRepository->method('findByContributionTo')->willReturn($fakeDbContributions);

        $contributions = $this->service()->findByContributionToProgramme($this->createMock(Programme::class));

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
