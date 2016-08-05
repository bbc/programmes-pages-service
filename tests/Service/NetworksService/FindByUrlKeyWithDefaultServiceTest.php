<?php

namespace Tests\BBC\ProgrammesPagesService\Service\NetworksService;

class FindByUrlKeyWithDefaultServiceTest extends AbstractNetworksServiceTest
{
    public function testFind()
    {
        $urlKey = 'radio2';

        $dbData = ['nid' => 'bbc_radio_two'];

        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyWithDefaultService')
            ->with($urlKey)
            ->willReturn($dbData);

        $result = $this->service()->findByUrlKeyWithDefaultService($urlKey);
        $this->assertEquals($this->networkFromDbData($dbData), $result);
    }

    public function testFindNoResult()
    {
        $urlKey = 'radionope';
        $this->mockRepository->expects($this->once())
            ->method('findByUrlKeyWithDefaultService')
            ->with($urlKey)
            ->willReturn(null);

        $result = $this->service()->findByUrlKeyWithDefaultService($urlKey);
        $this->assertNull($result);
    }
}
