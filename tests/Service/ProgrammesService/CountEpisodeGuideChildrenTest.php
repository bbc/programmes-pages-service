<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class CountEpisodeGuideChildrenTest extends AbstractProgrammesServiceTest
{
    public function testCommunicationOfServiceWithRepository()
    {
        $programme = $this->createConfiguredMock(Programme::class, ['getDbId' => 1]);

        $this->mockRepository->expects($this->once())
            ->method('countEpisodeGuideChildren')
            ->with($programme->getDbId());

        $this->service()->countEpisodeGuideChildren($programme);
    }

    /**
     * @dataProvider dbCountProvider
     */
    public function testCountReturnAnIntegerWithAmountOfEpisodes($dbCountProvided)
    {
        $programme = $this->createMock(Programme::class);

        $this->mockRepository
            ->method('countEpisodeGuideChildren')
            ->willReturn($dbCountProvided);

        $this->assertEquals($dbCountProvided, $this->service()->countEpisodeGuideChildren($programme));
    }

    public function dbCountProvider(): array
    {
        return [
            [0],
            [1],
            [10],
        ];
    }
}
