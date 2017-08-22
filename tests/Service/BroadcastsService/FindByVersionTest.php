<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindByVersionTest extends AbstractBroadcastsServiceTest
{
    public function testFindByVersionDefaultPagination()
    {
        $dbId = 1;
        $version = $this->createMock(Version::class);
        $version->method('getDbId')->willReturn($dbId);

        $this->mockRepository
            ->expects($this->once())
            ->method('findByVersion')
            ->with([$dbId], 'Broadcast', 300, 0);

        $this->service()->findByVersion($version);
    }

    public function testFindByVersionCustomPagination()
    {
        $dbId = 1;
        $version = $this->createMock(Version::class);
        $version->method('getDbId')->willReturn($dbId);

        $this->mockRepository
            ->expects($this->once())
            ->method('findByVersion')
            ->with([$dbId], 'Broadcast', 5, 10);

        $this->service()->findByVersion($version, 5, 3);
    }

    public function testFindByVersionWithNonExistantDbId()
    {
        $dbId = 999;
        $version = $this->createMock(Version::class);
        $version->method('getDbId')->willReturn($dbId);

        $this->mockRepository
            ->method('findByVersion')
            ->willReturn([]);

        $broadcasts = $this->service()->findByVersion($version, 5, 3);

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
