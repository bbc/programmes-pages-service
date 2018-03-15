<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindAvailableByProgrammeItemTest extends AbstractVersionsServiceTest
{
    public function testRepositoryReceiveProperParams()
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 101]);

        $this->mockRepository->expects($this->once())
            ->method('findAvailableByProgrammeItem')
            ->with($programmeItem->getDbId());

        $this->service()->findAvailableByProgrammeItem($programmeItem);
    }

    public function testVersionsAreReturnedWhenFound()
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 101]);

        $this->mockRepository->method('findAvailableByProgrammeItem')->willReturn([['pid' => 'b06tl314'], ['pid' => 'b06ts0v9']]);

        $versions = $this->service()->findAvailableByProgrammeItem($programmeItem);

        $this->assertCount(2, $versions);
        $this->assertContainsOnly(Version::class, $versions);
        $this->assertEquals('b06tl314', $versions[0]->getPid());
        $this->assertEquals('b06ts0v9', $versions[1]->getPid());
    }

    public function testEmptyArrayIsReceivedWhenNotFound()
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 101]);

        $this->mockRepository->method('findAvailableByProgrammeItem')->willReturn([]);

        $versions = $this->service()->findAvailableByProgrammeItem($programmeItem);

        $this->assertEquals([], $versions);
    }
}
