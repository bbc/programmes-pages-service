<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ImagesService;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;

class FindByPidTest extends AbstractImagesServiceTest
{
    public function testImageCanBeReceived()
    {
        $this->mockRepository->method('findByPid')->willReturn(['pid' => 'b010t19z']);

        $image = $this->service()->findByPid($this->createMock(Pid::class));

        $this->assertInstanceOf(Image::class, $image);
        $this->assertEquals('b010t19z', (string) $image->getPid());
    }

    public function testNullValueIsReceivedWhenNoResults()
    {
        $this->mockRepository->method('findByPid')->willReturn(null);

        $coreEntity = $this->service()->findByPid($this->createMock(Pid::class));

        $this->assertNull($coreEntity);
    }
}
