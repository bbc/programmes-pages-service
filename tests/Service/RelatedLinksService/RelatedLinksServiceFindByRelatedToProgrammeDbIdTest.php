<?php

namespace Tests\BBC\ProgrammesPagesService\Service\RelatedLinksService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class RelatedLinksServiceFindByRelatedToProgrammeDbIdTest extends AbstractRelatedLinksServiceTest
{
    public function testFindByRelatedToProgrammeDbIdDefaultPagination()
    {
        $dbId = 101;

        $dbData = [['title' => 'RelatedLink1'], ['title' => 'RelatedLink2']];

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$dbId], 'programme', 50, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByRelatedToProgrammeDbId($dbId);
        $this->assertEquals($this->relatedLinksFromDbData($dbData), $result);
    }

    public function testFindByRelatedToProgrammeDbIdCustomPagination()
    {
        $dbId = 101;

        $dbData = [['title' => 'RelatedLink1'], ['title' => 'RelatedLink2']];

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$dbId], 'programme', 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByRelatedToProgrammeDbId($dbId, 5, 3);
        $this->assertEquals($this->relatedLinksFromDbData($dbData), $result);
    }

    public function testFindByRelatedToProgrammeDbIdWithNonExistantPid()
    {
        $dbId = 999;

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$dbId], 'programme', 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByRelatedToProgrammeDbId($dbId, 5, 3);
        $this->assertEquals([], $result);
    }
}
