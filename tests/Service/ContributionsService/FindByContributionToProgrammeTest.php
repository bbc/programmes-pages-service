<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

class FindByContributionToProgrammeTest extends AbstractContributionsServiceTest
{
    public function testFindByContributionToProgrammeDefaultPagination()
    {
        $dbId = 1;
        $programme = $this->mockEntity('Programme', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'programme', false, 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToProgramme($programme);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToProgrammeCustomPagination()
    {
        $dbId = 1;
        $programme = $this->mockEntity('Programme', $dbId);
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'programme', false, 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByContributionToProgramme($programme, 5, 3);
        $this->assertEquals($this->contributionsFromDbData($dbData), $result);
    }

    public function testFindByContributionToProgrammeWithNonExistantDbId()
    {
        $dbId = 999;
        $programme = $this->mockEntity('Programme', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findByContributionTo')
            ->with([$dbId], 'programme', false, 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByContributionToProgramme($programme, 5, 3);
        $this->assertEquals([], $result);
    }
}
