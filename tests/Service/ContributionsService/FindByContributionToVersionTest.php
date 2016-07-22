<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

class FindByContributionToVersionTest extends AbstractContributionsServiceTest
{
    public function testFindByContributionToVersionDefaultPagination()
    {
        $dbId = 1;
        $version = $this->mockEntity('Version', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'version', 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToVersion($version);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToVersionCustomPagination()
    {
        $dbId = 1;
        $version = $this->mockEntity('Version', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'version', 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToVersion($version, 5, 3);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToVersionWithNonExistantDbId()
    {
        $dbId = 999;
        $version = $this->mockEntity('Version', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'version', 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByContributionToVersion($version, 5, 3);
        $this->assertEquals([], $result);
    }
}
