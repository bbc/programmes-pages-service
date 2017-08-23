<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindByVersionTest extends AbstractBroadcastsServiceTest
{
    /**
     * @dataProvider paginationProvider
     */
    public function testFindByServiceAndDateRangePagination($expectedLimit, $expectedOffset, $paginationParams)
    {
        $dbId = 1;
        $stubVersion = $this->createMock(Version::class);
        $stubVersion->method('getDbId')->willReturn($dbId);

        $this->mockRepository
            ->expects($this->once())
            ->method('findByVersion')
            ->with([$dbId], 'Broadcast', $expectedLimit, $expectedOffset);

        $this->service()->findByVersion($stubVersion, ...$paginationParams);
    }

    public function paginationProvider()
    {
        return [
            'default pagination' => [300, 0, []],
            'custom pagination' => [5, 10, [5, 3]],
        ];
    }

    public function testFindByVersionWithNonExistantDbId()
    {
        $dbId = 999;
        $stubVersion = $this->createMock(Version::class);
        $stubVersion->method('getDbId')->willReturn($dbId);

        $this->mockRepository
            ->method('findByVersion')
            ->willReturn([]);

        $broadcasts = $this->service()->findByVersion($stubVersion);

        $this->assertEquals([], $broadcasts);
    }

    public function testFindByVersionWithExistantDbId()
    {
        $dbId = 1;
        $version = $this->createMock(Version::class);
        $version->method('getDbId')->willReturn($dbId);

        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];

        $this->mockRepository
            ->method('findByVersion')
            ->willReturn($dbData);

        $broadcasts = $this->service()->findByVersion($version);

        $this->assertCount(2, $broadcasts);
        $this->assertContainsOnly(Broadcast::class, $broadcasts);
        $this->assertEquals('b00swyx1', (string) $broadcasts[0]->getPid());
        $this->assertEquals('b010t150', (string) $broadcasts[1]->getPid());
    }
}
