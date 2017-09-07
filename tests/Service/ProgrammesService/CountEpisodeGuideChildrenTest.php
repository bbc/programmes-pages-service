<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class CountEpisodeGuideChildrenTest extends AbstractProgrammesServiceTest
{
    public function testProtocolWithRepository()
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('countEpisodeGuideChildren')
            ->with($programme->getDbId());

        $this->service()->countEpisodeGuideChildren($programme);
    }

    public function testCountReturnAnIntegerWithAmountOfEpisodes()
    {
        $programme = $this->createMock(Programme::class);

        $this->mockRepository
            ->method('countEpisodeGuideChildren')
            ->will($this->onConsecutiveCalls(0, 1, 10));

        $this->assertEquals(0, $this->service()->countEpisodeGuideChildren($programme));
        $this->assertEquals(1, $this->service()->countEpisodeGuideChildren($programme));
        $this->assertEquals(10, $this->service()->countEpisodeGuideChildren($programme));
    }
}
