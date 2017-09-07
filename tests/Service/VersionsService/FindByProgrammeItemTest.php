<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindByProgrammeItemTest extends AbstractVersionsServiceTest
{
    public function testVersionsAreReturnedWhenFound()
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 101]);

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeItem')
            ->with($programmeItem->getDbId())
            ->willReturn([['pid' => 'b06tl314'], ['pid' => 'b06ts0v9']]);

        $versions = $this->service()->findByProgrammeItem($programmeItem);

        $this->assertCount(2, $versions);
        $this->assertContainsOnly(Version::class, $versions);
    }

    public function testEmptyArrayIsReceivedWhenNotFound()
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 101]);

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeItem')
            ->with($programmeItem->getDbId())
            ->willReturn([]);

        $versions = $this->service()->findByProgrammeItem($programmeItem);

        $this->assertEquals([], $versions);
    }
}
