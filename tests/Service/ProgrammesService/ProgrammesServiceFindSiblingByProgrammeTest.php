<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;

class ProgrammesServiceFindSiblingByProgrammeTest extends AbstractProgrammesServiceTest
{
    public function testFindNextSiblingByProgrammeSearchesByPosition()
    {
        $programme = $this->getMockEpisode(1, 3, null);
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'next')
            ->willReturn($dbData);

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByReleaseDate');

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindNextSiblingByProgrammeSearchesByReleaseDate()
    {
        $programme = $this->getMockEpisode(1, null, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByPosition');

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByReleaseDate')
            ->with(1, new PartialDate('2016'), 'Episode', 'next')
            ->willReturn($dbData);

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindNextSiblingByProgrammeSearchesByFirstBroadcastDate()
    {
        $programme = $this->getMockEpisode(1, null, null, new \DateTimeImmutable('2000-01-01 00:00:00'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByPosition');

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByReleaseDate');

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByFirstBroadcastDate')
            ->with(1, new \DateTimeImmutable('2000-01-01 00:00:00'), 'Episode', 'next')
            ->willReturn($dbData);

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindNextSiblingByProgrammePrefersSearchingByPosition()
    {
        $programme = $this->getMockEpisode(1, 3, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'next')
            ->willReturn($dbData);

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByReleaseDate');

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindNextSiblingByProgrammeFallsBackToReleaseDate()
    {
        $programme = $this->getMockEpisode(1, 3, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'next')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByReleaseDate')
            ->with(1, new PartialDate('2016'), 'Episode', 'next')
            ->willReturn($dbData);

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindNextSiblingByProgrammeFallsBackToFirstBroadcastDate()
    {
        $programme = $this->getMockEpisode(1, 3, null, new \DateTimeImmutable('2016-01-01 00:00:00'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'next')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByFirstBroadcastDate')
            ->with(1, new \DateTimeImmutable('2016-01-01 00:00:00'), 'Episode', 'next')
            ->willReturn($dbData);

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindNextSiblingByProgrammeReturnsNullIfNoResult()
    {
        $programme = $this->getMockEpisode(1, 3, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'next')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByReleaseDate')
            ->with(1, new PartialDate('2016'), 'Episode', 'next')
            ->willReturn(null);

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertNull($result);
    }

    public function testFindNextSiblingByProgrammeReturnsEarlyIfNoParent()
    {
        $programme = $this->getMockEpisode(null, 3, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByPosition');

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByReleaseDate');

        $result = $this->service()->findNextSiblingByProgramme($programme);
        $this->assertNull($result);
    }

    public function testFindPreviousSiblingByProgrammeSearchesByPosition()
    {
        $programme = $this->getMockEpisode(1, 3, null);
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'previous')
            ->willReturn($dbData);

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByReleaseDate');

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindPreviousSiblingByProgrammeSearchesByReleaseDate()
    {
        $programme = $this->getMockEpisode(1, null, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByPosition');

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByReleaseDate')
            ->with(1, new PartialDate('2016'), 'Episode', 'previous')
            ->willReturn($dbData);

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindPreviousSiblingByProgrammePrefersSearchingByPositionIfPositionAndReleaseDateArePresent()
    {
        $programme = $this->getMockEpisode(1, 3, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'previous')
            ->willReturn($dbData);

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByReleaseDate');

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindPreviousSiblingByProgrammeFallsBackToReleaseDateIfPositionAndReleaseDateArePresent()
    {
        $programme = $this->getMockEpisode(1, 3, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'previous')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByReleaseDate')
            ->with(1, new PartialDate('2016'), 'Episode', 'previous')
            ->willReturn($dbData);

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertEquals($this->programmeFromDbData($dbData), $result);
    }

    public function testFindPreviousSiblingByProgrammeReturnsNullIfNoResult()
    {
        $programme = $this->getMockEpisode(1, 3, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(1, 3, 'Episode', 'previous')
            ->willReturn(null);

        $this->mockRepository->expects($this->once())
            ->method('findAdjacentProgrammeByReleaseDate')
            ->with(1, new PartialDate('2016'), 'Episode', 'previous')
            ->willReturn(null);

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertNull($result);
    }

    public function testFindPreviousSiblingByProgrammeReturnsEarlyIfNoParent()
    {
        $programme = $this->getMockEpisode(null, 3, new PartialDate('2016'));
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByPosition');

        $this->mockRepository->expects($this->never())
            ->method('findAdjacentProgrammeByReleaseDate');

        $result = $this->service()->findPreviousSiblingByProgramme($programme);
        $this->assertNull($result);
    }

    private function getMockEpisode($parentId = null, $position = null, $releaseDate = null, $firstBroadcastDate = null)
    {
        $programme = $this->mockEntity('Episode');
        $programme->method('getPosition')->willReturn($position);
        $programme->method('getReleaseDate')->willReturn($releaseDate);
        $programme->method('getFirstBroadcastDate')->willReturn($firstBroadcastDate);

        if ($parentId) {
            $parent = $this->mockEntity('Series');
            $parent->method('getDbId')->willReturn($parentId);
            $programme->method('getParent')->willReturn($parent);
        }

        return $programme;
    }
}
