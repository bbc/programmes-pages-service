<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;


class FindPreviousSiblingByProgramme
{
    public function testFindPreviousSiblingByProgrammeSearchesByPosition()
    {
        $programme = $this->getMockEpisode(1, 3, null);
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'previous')
            ->willReturn($dbData);

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByFirstBroadcastDate');

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindPreviousSiblingByProgrammeReturnsNullIfNoResult()
    {
        $programme = $this->getMockEpisode(1, 3, new PartialDate(2016));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'previous')
            ->willReturn(null);

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertNull($result);
    }

    public function testFindPreviousSiblingByProgrammeReturnsEarlyIfNoParent()
    {
        $programme = $this->getMockEpisode(null, 3, new PartialDate(2016));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByPosition');

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertNull($result);
    }
}
