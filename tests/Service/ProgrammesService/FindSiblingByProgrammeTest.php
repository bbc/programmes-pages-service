<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use DateTime;
use DateTimeImmutable;

class FindSiblingByProgrammeTest extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider positionParamProvider
     */
    public function testAsLongAsWeCanFindByPositionThenNeverIsSearchedByBroadcastedDate($positionProvided, $releaseDateProvided, $firstBroadcastDate)
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
            )
            ->willReturn(['pid' => 'b010t19z']);

        $this->service()->findNextSiblingByProgramme($episode);
    }

    public function positionParamProvider(): array
    {
        return [
            'CASE: default search is by position when also release date is passed' => [3, null, null],
            'CASE: find adjacent programmes by position when this is passed' => [3, new Datetime(), null],
            'CASE: find adjacent programmes by position when this is passed again' => [3, new Datetime(), new DateTimeImmutable()],
        ];
    }

    /**
     * @dataProvider firstBroadcastedDateParamProvider
     */
    public function testWhenNoResultsSearchingByPositionThenWeTryByBroadcastedDate($positionProvided, $releaseDateProvided, $firstBroadcastDateProvided)
    {
        $episode = $this->getMockEpisode(1, $positionProvided, $releaseDateProvided, $firstBroadcastDateProvided);

        $this->mockRepository
            ->expects($this->once())
            ->method('findAdjacentProgrammeByFirstBroadcastDate')
            ->with(
                $episode->getParent()->getDbId(),
                $firstBroadcastDateProvided,
                'Episode',
                'next'
            );

        $this->mockRepository
            ->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->willReturn(null);

        $this->service()->findNextSiblingByProgramme($episode);
    }

    public function firstBroadcastedDateParamProvider(): array
    {
        return [
            'CASE: default search is by position when also release date is passed' => [3, null, new DateTimeImmutable()],
            'CASE: find adjacent programmes by position when this is passed' => [3, new Datetime(), new DateTimeImmutable()],
        ];
    }


    private function getMockEpisode($parentId = null, $position = null, $releaseDate = null, $firstBroadcastDate = null)
    {
        $episode = $this->createConfiguredMock(Episode::class, [
            'getPosition' => $position,
            'getReleaseDate' => $releaseDate,
            'getFirstBroadcastDate' => $firstBroadcastDate
        ]);

        if ($parentId) {
            $episode->method('getParent')->willReturn(
                $this->createConfiguredMock(Series::class,['getDbId' => $parentId])
            );
        }

        return $episode;
    }
}
