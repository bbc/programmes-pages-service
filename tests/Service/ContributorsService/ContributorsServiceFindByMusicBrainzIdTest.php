<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributorsService;

use BBC\ProgrammesPagesService\Service\ContributorsService;

class ContributorsServiceFindByMusicBrainzIdTest extends AbstractContributorsServiceTest
{
    public function testFindByMusicBrainzId()
    {
        $mbid = '7746d775-9550-4360-b8d5-c37bd448ce01';
        $dbData = ['musicBrainzId' => $mbid];

        $this->mockRepository->expects($this->once())
            ->method('findByMusicBrainzId')
            ->with($mbid)
            ->willReturn($dbData);

        $result = $this->service()->findByMusicBrainzId($mbid);
        $this->assertEquals($this->contributorFromDbData($dbData), $result);
    }

    public function testFindByMusicBrainzIdEmptyData()
    {
        $mbid = '7746d775-9550-4360-b8d5-c37bd448ce01';

        $this->mockRepository->expects($this->once())
            ->method('findByMusicBrainzId')
            ->with($mbid)
            ->willReturn(null);

        $result = $this->service()->findByMusicBrainzId($mbid);
        $this->assertNull($result);
    }

    protected function service()
    {
        return new ContributorsService($this->mockRepository, $this->mockMapper);
    }
}
