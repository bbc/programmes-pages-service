<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use DateTime;
use DateTimeImmutable;

class FindSiblingByProgrammeTest extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider paramsProvider
     */
    public function testWhenPositionPassedIsCalledByPositionFunction($positionProvided, $releaseDateProvided, $firstBroadcastDate)
    {
        $episode = $this->getMockEpisode(1, $positionProvided, $releaseDateProvided, $firstBroadcastDate);

        $this->mockRepository
            ->expects($this->never())
            ->method('findAdjacentProgrammeByFirstBroadcastDate');

        $this->mockRepository
            ->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->with(
                $episode->getParent()->getDbId(),
                $positionProvided,
                'Episode',
                'next'
            );

        $this->service()->findNextSiblingByProgramme($episode);
    }

    public function paramsProvider(): array
    {
        return [
          'CASE: find adjacent programmes by position when this is passed' => [3, new Datetime(), null],
          'CASE: default search is by position when also release date is passed' => [3, null, null],
        ];
    }

    /**
     * @dataProvider byBroadcastedDateParamsProvider
     */
    public function testWhenPositionIsNullAndFirstBroadcastedDateIsPassedThenIsSearchedByBroadcastedDate($positionProvided, $releaseDateProvided, $firstBroadcastDateProvided)
    {
        $episode = $this->getMockEpisode(1, $positionProvided, $releaseDateProvided, $firstBroadcastDateProvided);

        $this->mockRepository
            ->expects($this->never())
            ->method('findAdjacentProgrammeByPosition');

        $this->mockRepository
            ->expects($this->once())
            ->method('findAdjacentProgrammeByFirstBroadcastDate')
            ->with(
                $episode->getParent()->getDbId(),
                $firstBroadcastDateProvided,
                'Episode',
                'next'
            );

        $this->service()->findNextSiblingByProgramme($episode);
    }

    public function byBroadcastedDateParamsProvider(): array
    {
        return [
            'CASE: find adjacent programmes by position when this is passed' => [null, new Datetime(), new DateTimeImmutable()],
            'CASE: default search is by position when also release date is passed' => [null, null, new DateTimeImmutable()],
        ];
    }


    public function testFindNextSiblingByProgrammeReturnsNullIfNoResult()
    {
        $position = 3;
        $releaseDate = new PartialDate(2016);
        $programme = $this->getMockEpisode(1, $position, $releaseDate);

        $this->mockRepository->method('findAdjacentProgrammeByPosition')->willReturn(null);

        $result = $this->service()->findNextSiblingByProgramme($programme);

        $this->assertNull($result);
    }

    private function getMockEpisode($parentId = null, $position = null, $releaseDate = null, $firstBroadcastDate = null)
    {
        $episode = $this->createConfiguredMock(Episode::class, [
            'getPosition' => $position,
            'getReleaseDate' => $releaseDate,
            'getFirstBroadcastDate' => $firstBroadcastDate
        ]);

        if ($parentId) {
            $parent = $this->createConfiguredMock(Series::class,['getDbId' => $parentId]);
            $episode->method('getParent')->willReturn($parent);
        }

        return $episode;
    }
}
