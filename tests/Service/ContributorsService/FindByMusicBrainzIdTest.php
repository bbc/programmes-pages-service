<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributorsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contributor;

class FindByMusicBrainzIdTest extends AbstractContributorsServiceTest
{
    public function testFindByMusicBrainzIdInteraction()
    {
        $mbid = '7746d775-9550-4360-b8d5-c37bd448ce01';

        $this->mockRepository->expects($this->once())
             ->method('findByMusicBrainzId')
             ->with($mbid);

        $this->service()->findByMusicBrainzId($mbid);
    }

    public function testFindByMusicBrainzIdResultsFound()
    {
        $this->mockRepository
            ->method('findByMusicBrainzId')
            ->willReturn(['musicBrainzId' => '7746d775-9550-4360-b8d5-c37bd448ce01']);

        $contributor = $this->service()->findByMusicBrainzId('7746d775-9550-4360-b8d5-c37bd448ce01');

        $this->assertInstanceOf(Contributor::class, $contributor);
    }

    public function testFindByMusicBrainzIdNoResults()
    {
        $this->mockRepository
            ->method('findByMusicBrainzId')
            ->willReturn(null);

        $contributor = $this->service()->findByMusicBrainzId('7746d775-9550-4360-b8d5-c37bd448ce01');

        $this->assertNull($contributor);
    }
}
