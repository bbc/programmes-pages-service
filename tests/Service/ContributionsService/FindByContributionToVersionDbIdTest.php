<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

class FindByContributionToVersionDbIdTest extends AbstractContributionsServiceTest
{
    public function testFindByContributionToVersionDbIdDefaultPagination()
    {
        $dbId = 1;
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'version', 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToVersionDbId($dbId);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToVersionDbIdCustomPagination()
    {
        $dbId = 1;
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'version', 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToVersionDbId($dbId, 5, 3);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToVersionDbIdWithNonExistantDbId()
    {
        $dbId = 999;

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'version', 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByContributionToVersionDbId($dbId, 5, 3);
        $this->assertEquals([], $result);
    }
}
