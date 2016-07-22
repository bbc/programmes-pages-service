<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

class FindByProgrammeItemTest extends AbstractVersionsServiceTest
{
    public function testFindByProgrammeItem()
    {
        $dbId = 101;
        $programmeItem = $this->mockEntity('ProgrammeItem', $dbId);
        $dbData = [['pid' => 'b06tl314'], ['pid' => 'b06ts0v9']];

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeItem')
            ->with($dbId)
            ->willReturn($dbData);

        $result = $this->service()->findByProgrammeItem($programmeItem);
        $this->assertEquals($this->versionsFromDbData($dbData), $result);
    }

    public function testFindByProgrammeItemDbIdWithNonExistantItem()
    {
        $dbId = 999;
        $programmeItem = $this->mockEntity('ProgrammeItem', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeItem')
            ->with($dbId)
            ->willReturn([]);

        $result = $this->service()->findByProgrammeItem($programmeItem);
        $this->assertEquals([], $result);
    }
}
