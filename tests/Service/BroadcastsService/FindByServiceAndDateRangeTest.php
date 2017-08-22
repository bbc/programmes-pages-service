<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;

class FindByServiceAndDateRangeTest extends AbstractBroadcastsServiceTest
{
    public function testFindByServiceAndDateRange()
    {
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];
        $fromDateTime = new DateTimeImmutable('-1 year');
        $toDatetime = new DateTimeImmutable('+1 year');
        $sid = new Sid('bbc_radio_two');

        $this->mockRepository->expects($this->once())
            ->method('findAllByServiceAndDateRange')
            ->with($sid, $fromDateTime, $toDatetime, -1, 0)
            ->willReturn($dbData);

        $broadcasts = $this->service()->findByServiceAndDateRange($sid, $fromDateTime, $toDatetime, -1, 1);

        $this->assertCount(2, $broadcasts);
        $this->assertContainsOnly(Broadcast::class, $broadcasts);
        $this->assertEquals('b00swyx1', (string) $broadcasts[0]->getPid());
        $this->assertEquals('b010t150', (string) $broadcasts[1]->getPid());
    }

    public function testFindByServiceAndDateRangeWhenNoBroadcastsFound()
    {
        $fromDateTime = new DateTimeImmutable('-1 year');
        $toDatetime = new DateTimeImmutable('+1 year');
        $sid = new Sid('this_sid_doesnt_exist');

        $this->mockRepository->expects($this->once())
            ->method('findAllByServiceAndDateRange')
            ->with($sid, $fromDateTime, $toDatetime, -1, 0)
            ->willReturn([]);

        $broadcasts = $this->service()->findByServiceAndDateRange($sid, $fromDateTime, $toDatetime, -1, 1);

        $this->assertSame([], $broadcasts);
    }
}
