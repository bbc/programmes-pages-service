<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Episode;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use DateTime;
use DateTimeImmutable;

class FindPreviousSiblingByProgramme extends AbstractProgrammesServiceTest
{
    /**
     * @dataProvider positionParamProvider
     */
    public function testAsLongAsWeCanFindProgrammesByPositionThenNeverIsSearchedByBroadcastedDate($positionProvided, $releaseDateProvided, $firstBroadcastDate)
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
                'previous'
            )
            ->willReturn(['pid' => 'b010t19z']);

        $this->service()->findPreviousSiblingByProgramme($episode);
    }

    public function positionParamProvider(): array
    {
        return [
            'CASE: default search is by position when also release date is passed' => [3, null, null],
            'CASE: find adjacent programmes by position when this is passed' => [3, new Datetime(), null],
            'CASE: if search by position has results, then we dont try to search by broadcasted date' => [3, new Datetime(), new DateTimeImmutable()],
        ];
    }

    /**
     * @dataProvider firstBroadcastedDateParamProvider
     */
    public function testWhenNoProgrammesResultsSearchingByPositionThenWeTryByBroadcastedDate($positionProvided, $releaseDateProvided, $firstBroadcastDateProvided)
    {
        $episode = $this->getMockEpisode(1, $positionProvided, $releaseDateProvided, $firstBroadcastDateProvided);

        $this->mockRepository
            ->expects($this->once())
            ->method('findAdjacentProgrammeByFirstBroadcastDate')
            ->with(
                $episode->getParent()->getDbId(),
                $firstBroadcastDateProvided,
                'Episode',
                'previous'
            );

        $this->mockRepository
            ->expects($this->once())
            ->method('findAdjacentProgrammeByPosition')
            ->willReturn(null);

        $this->service()->findPreviousSiblingByProgramme($episode);
    }

    public function firstBroadcastedDateParamProvider(): array
    {
        return [
            'CASE: default search by broadcasted date is attempted after trying by position' => [3, null, new DateTimeImmutable()],
            'CASE: default search by broadcasted date is attempted after trying by position again' => [3, new Datetime(), new DateTimeImmutable()],
        ];
    }

    public function testProgrammesAreReceivedBySearchByPosition()
    {
        $programme = $this->getMockEpisode(1, 3, null, null);

        $this->mockRepository->method('findAdjacentProgrammeByPosition')->willReturn(['pid' => 'b010t19z']);

        $programme = $this->service()->findPreviousSiblingByProgramme($programme);

        $this->assertInstanceOf(Programme::class, $programme);
        $this->assertEquals('b010t19z', $programme->getPid());
    }

    public function testProgrammesAreReceivedBySearchByBroadcastDate()
    {
        $programme = $this->getMockEpisode(1, null, null, new DateTimeImmutable());

        $this->mockRepository->method('findAdjacentProgrammeByFirstBroadcastDate')->willReturn(['pid' => 'b010t19z']);

        $programme = $this->service()->findPreviousSiblingByProgramme($programme);

        $this->assertInstanceOf(Programme::class, $programme);
        $this->assertEquals('b010t19z', $programme->getPid());
    }

    private function getMockEpisode($parentId = null, $position = null, $releaseDate = null, $firstBroadcastDate = null)
    {
        $episode = $this->createConfiguredMock(Episode::class, [
            'getPosition' => $position,
            'getReleaseDate' => $releaseDate,
            'getFirstBroadcastDate' => $firstBroadcastDate,
        ]);

        if ($parentId) {
            $episode->method('getParent')->willReturn(
                $this->createConfiguredMock(Series::class, ['getDbId' => $parentId])
            );
        }

        return $episode;
    }
}
