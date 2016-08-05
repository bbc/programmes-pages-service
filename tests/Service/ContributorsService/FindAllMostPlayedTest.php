<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributorsService;

use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;

class FindAllMostPlayedTest extends AbstractContributorsServiceTest
{
    public function testAll()
    {
        $from = new \DateTimeImmutable('2016-06-01');
        $to = new \DateTimeImmutable('2016-06-07');

        $mbid = '7746d775-9550-4360-b8d5-c37bd448ce01';
        $mbid2 = '9c9f1380-2516-4fc9-a3e6-f9f61941d090';
        $dbData = [
            [
                0 => ['musicBrainzId' => $mbid],
                'contributorPlayCount' => 10,
            ],
            [
                0 => ['musicBrainzId' => $mbid2],
                'contributorPlayCount' => 5,
            ],
        ];

        $this->mockRepository->expects($this->once())
            ->method('findAllMostPlayedWithPlays')
            ->with($from, $to, null)
            ->willReturn($dbData);

        $results = $this->service()->findAllMostPlayed($from, $to);
        $this->assertSame(2, count($results));
        $this->assertEquals($this->contributorFromDbData($dbData[0][0]), $results[0]->contributor);
        $this->assertEquals($this->contributorFromDbData($dbData[1][0]), $results[1]->contributor);
        $this->assertSame(10, $results[0]->plays);
        $this->assertSame(5, $results[1]->plays);
    }

    public function testAllEmpty()
    {
        $from = new \DateTimeImmutable('2016-06-01');
        $to = new \DateTimeImmutable('2016-06-07');

        $dbData = [];

        $this->mockRepository->expects($this->once())
            ->method('findAllMostPlayedWithPlays')
            ->with($from, $to, null)
            ->willReturn($dbData);

        $results = $this->service()->findAllMostPlayed($from, $to);
        $this->assertEquals([], $results);
    }

    public function testByService()
    {
        $from = new \DateTimeImmutable('2016-06-01');
        $to = new \DateTimeImmutable('2016-06-07');
        $service = new Service(1, new Sid('sid'), 'name');

        $mbid = '6746d775-9550-4360-b8d5-c37bd448ce01';
        $dbData = [
            [
                0 => ['musicBrainzId' => $mbid],
                'contributorPlayCount' => 10,
            ],
        ];

        $this->mockRepository->expects($this->once())
            ->method('findAllMostPlayedWithPlays')
            ->with($from, $to, 1)
            ->willReturn($dbData);

        $results = $this->service()->findAllMostPlayed($from, $to, $service);
        $this->assertSame(1, count($results));
        $this->assertEquals($this->contributorFromDbData($dbData[0][0]), $results[0]->contributor);
        $this->assertSame(10, $results[0]->plays);
    }
}
