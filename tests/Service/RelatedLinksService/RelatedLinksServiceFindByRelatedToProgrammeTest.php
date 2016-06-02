<?php

namespace Tests\BBC\ProgrammesPagesService\Service\RelatedLinksService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class RelatedLinksServiceFindByRelatedToProgrammeTest extends AbstractRelatedLinksServiceTest
{
    public function testFindByRelatedToProgrammeDefaultPagination()
    {
        $pid = new Pid('b010t19z');

        $dbData = [['title' => 'RelatedLink1'], ['title' => 'RelatedLink2']];

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$pid], 'programme', 50, 0)
            ->willReturn($dbData);

        $result = $this->service()->findByRelatedToProgramme($pid);
        $this->assertEquals($this->relatedLinksFromDbData($dbData), $result);
    }

    public function testFindByRelatedToProgrammeCustomPagination()
    {
        $pid = new Pid('b010t19z');

        $dbData = [['title' => 'RelatedLink1'], ['title' => 'RelatedLink2']];

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$pid], 'programme', 5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findByRelatedToProgramme($pid, 5, 3);
        $this->assertEquals($this->relatedLinksFromDbData($dbData), $result);
    }

    public function testFindByRelatedToProgrammeWithNonExistantPid()
    {
        $pid = new Pid('qqqqqqqq');

        $this->mockRepository->expects($this->once())
            ->method('findByRelatedTo')
            ->with([$pid], 'programme', 5, 10)
            ->willReturn([]);

        $result = $this->service()->findByRelatedToProgramme($pid, 5, 3);
        $this->assertEquals([], $result);
    }
}
