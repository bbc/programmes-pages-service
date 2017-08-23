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
     * @dataProvider repositoryResultsProvider
     */
    public function testFindByVersionResults(array $expectedPids, array $stubRepositoryResults)
    {
        $this->mockRepository->method('findByVersion')->willReturn($stubRepositoryResults);

        $dummyVersion = $this->createMock(Version::class);
        $broadcasts = $this->service()->findByVersion($dummyVersion);

        $this->assertContainsOnly(Broadcast::class, $broadcasts);
        $this->assertSame($expectedPids, $this->extractPids($broadcasts));
    }

    public function repositoryResultsProvider(): array
    {

        return [
            // [expectations], [results]
            'with results' => [['b00swyx1', 'b010t150'], [['pid' => 'b00swyx1'], ['pid' => 'b010t150']]],
            'empty results' => [[], []],
        ];
    }
}
