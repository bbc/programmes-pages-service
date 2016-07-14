<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

class ContributorsServiceFindByMusicBrainzIdTest extends AbstractServiceTest
{
    public function testFindByMusicBrainzId()
    {
        $mbid = '7746d775-9550-4360-b8d5-c37bd448ce01';
        $dbData = ['id' => $mbid];

        $this->mockRepository->expects($this->once())
            ->method('findByMusicBrainzId')
            ->with($mbid)
            ->willReturn($dbData);

        $mockContributor = $this->createMock(self::ENTITY_NS . 'Contributor');

        $mockContributor->method('getMusicBrainzId')->willReturn($mbid);

        $result = $this->service()->findByMusicBrainzId($mbid);
        $this->assertEquals($mockContributor, $result);
    }

    public function testFindByMusicBrainzIdEmptyData()
    {
        $mbid = '7746d775-9550-4360-b8d5-c37bd448ce01';

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($mbid)
            ->willReturn(null);

        $result = $this->service()->findByMusicBrainzId($mbid);
        $this->assertNull($result);
    }
}
