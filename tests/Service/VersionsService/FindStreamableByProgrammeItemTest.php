<?php

namespace Tests\BBC\ProgrammesPagesService\Service\VersionsService;

use BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem;
use BBC\ProgrammesPagesService\Domain\Entity\Version;

class FindStreamableByProgrammeItemTest extends AbstractVersionsServiceTest
{
    public function testRepositoryReceiveProperParams()
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 101]);

        $this->mockRepository->expects($this->once())
            ->method('findByProgrammeItem')
            ->with($programmeItem->getDbId());

        $this->service()->findByProgrammeItem($programmeItem);
    }

    public function testVersionsAreReturnedWhenFound()
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 101]);

        $this->mockRepository->method('findByProgrammeItem')->willReturn([['pid' => 'b06tl314'], ['pid' => 'b06ts0v9']]);

        $versions = $this->service()->findByProgrammeItem($programmeItem);

        $this->assertCount(2, $versions);
        $this->assertContainsOnly(Version::class, $versions);
        $this->assertEquals('b06tl314', $versions[0]->getPid());
        $this->assertEquals('b06ts0v9', $versions[1]->getPid());
    }

    public function testEmptyArrayIsReceivedWhenNotFound()
    {
        $programmeItem = $this->createConfiguredMock(ProgrammeItem::class, ['getDbId' => 101]);

        $this->mockRepository->method('findByProgrammeItem')->willReturn([]);

        $versions = $this->service()->findByProgrammeItem($programmeItem);

        $this->assertEquals([], $versions);
    }
}
