<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;


class CountEpisodeGuideChildrenTest extends AbstractProgrammesServiceTest
{
    public function testCountReturnAnIntegerWithAmountOfEpisodes()
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 1]);

        $this->mockRepository
            ->method('countEpisodeGuideChildren')
            ->with($programme->getDbId())
            ->will($this->onConsecutiveCalls(0, 1, 10));

        $this->assertEquals(0, $this->service()->countEpisodeGuideChildren($programme));
        $this->assertEquals(1, $this->service()->countEpisodeGuideChildren($programme));
        $this->assertEquals(10, $this->service()->countEpisodeGuideChildren($programme));
    }
}
