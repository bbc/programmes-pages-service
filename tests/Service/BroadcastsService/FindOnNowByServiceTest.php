<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Service;

class FindOnNowByServiceTest extends AbstractBroadcastsServiceTest
{
    /**
     * @dataProvider repositoryResultsProvider
     */
    public function testFindOnNowByServiceResults($expectedPid, $stubRepositoryResults)
    {
        $this->mockRepository
            ->method('findOnNowByService')
            ->willReturn($stubRepositoryResults);

        /** @var $broadcast Broadcast This mock shall always return a Broadcast, never null */
        $broadcast = $this->service()->findOnNowByService(
            $this->createMock(Service::class)
        );

        $this->assertInstanceOf(Broadcast::class, $broadcast);
        $this->assertEquals($expectedPid, $broadcast->getPid());
    }

    public function repositoryResultsProvider(): array
    {
        return [
            // [expectations], [results]
            ['b00swyx1', ['pid' => 'b00swyx1']],
            ['b010t150', ['pid' => 'b010t150']],
        ];
    }

    public function testFindOnNowByServiceEmptyResults()
    {
        $this->mockRepository
            ->method('findOnNowByService')
            ->willReturn(null);

        $broadcast = $this->service()->findOnNowByService(
            $this->createMock(Service::class)
        );

        $this->assertSame(null, $broadcast);
    }
}
