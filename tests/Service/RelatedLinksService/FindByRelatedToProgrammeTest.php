<?php

namespace Tests\BBC\ProgrammesPagesService\Service\RelatedLinksService;

class FindByRelatedToProgrammeTest extends AbstractRelatedLinksServiceTest
{
    public function testFindByRelatedToProgrammeDefaultPagination()
    {
        $dbId = 101;
        $programme = $this->mockEntity('Programme', $dbId);
        $dbData = [['title' => 'RelatedLink1'], ['title' => 'RelatedLink2']];

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$dbId], 'programme', 300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByRelatedToProgramme($programme);
        $this->assertEquals($this->relatedLinksFromDbData($dbData), $result);
    }

    public function testFindByRelatedToProgrammeCustomPagination()
    {
        $dbId = 101;
        $programme = $this->mockEntity('Programme', $dbId);
        $dbData = [['title' => 'RelatedLink1'], ['title' => 'RelatedLink2']];

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$dbId], 'programme', 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByRelatedToProgramme($programme, 5, 3);
        $this->assertEquals($this->relatedLinksFromDbData($dbData), $result);
    }

    public function testFindByRelatedToProgrammeWithNonExistantPid()
    {
        $dbId = 999;
        $programme = $this->mockEntity('Programme', $dbId);

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$dbId], 'programme', 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByRelatedToProgramme($programme, 5, 3);
        $this->assertEquals([], $result);
    }
}
