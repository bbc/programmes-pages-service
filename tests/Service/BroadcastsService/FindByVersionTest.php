<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindByVersionTest extends AbstractBroadcastsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testFindVersionPagination($expectedLimit, $expectedOffset, $paginationParams)
    {
        $stubVersion = $this->createMock(Version::class);
        $stubVersion->method('getDbId')->willReturn(1);

        $this->mockRepository->expects($this->once()) ->method('findByVersion')
            ->with([$stubVersion->getDbId()], 'Broadcast', $expectedLimit, $expectedOffset);

        $this->service()->findByVersion($stubVersion, ...$paginationParams);
    }

    public function paginationProvider(): array
    {
        return [
            // [expectedLimit, expectedOffset, [limit, page]]
            'default pagination' => [300, 0, []],
            'custom pagination' => [5, 10, [5, 3]],
        ];
    }

    /**
     * @dataProvider dbBroadcastsProvider
     */
    public function testFindByVersionResults(array $expectedPids, array $dbBroadcastsProvided)
    {
        $this->mockRepository->method('findByVersion')->willReturn($dbBroadcastsProvided);

        $dummyVersion = $this->createMock(Version::class);
        $broadcasts = $this->service()->findByVersion($dummyVersion);

        $this->assertCount(count($dbBroadcastsProvided), $broadcasts);
        $this->assertContainsOnly(Broadcast::class, $broadcasts);
        foreach ($expectedPids as $i => $expectedPid) {
            $this->assertEquals($expectedPid, $broadcasts[$i]->getPid());
        }
    }

    public function dbBroadcastsProvider(): array
    {

        return [
            // [expectations], [results]
            'with results' => [['b00swyx1', 'b010t150'], [['pid' => 'b00swyx1'], ['pid' => 'b010t150']]],
            'empty results' => [[], []],
        ];
    }
}
