<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;

class FindByServiceAndDateRangeTest extends AbstractBroadcastsServiceTest
{
    public function testFindByServiceAndDateRange()
    {
        $dbData = [['pid' => 'b00swyx1'], ['pid' => 'b010t150']];
        $fromDateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2010-01-15 06:00:00');
        $toDatetime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2017-10-16 06:00:00');
        $sid = new Sid('bbc_radio_two');

        $this->mockRepository->expects($this->once())
            ->method('findAllByServiceAndDateRange')
            ->with(
             $sid,
             $fromDateTime,
             $toDatetime,
             -1,
             0
            )->willReturn($dbData);

        $broadcasts = $this->service()->findByServiceAndDateRange(
            $sid,
            $fromDateTime,
            $toDatetime,
            -1,
            1
        );

        $this->assertInternalType('array', $broadcasts);
        $this->assertCount(2, $broadcasts);
        array_map(
            function($broadcast) {
                $this->assertInstanceOf(
                    'BBC\ProgrammesPagesService\Domain\Entity\Broadcast',
                    $broadcast
                );
            },

            $broadcasts
        );
    }

    public function testFindByServiceAndDateRangeWhenNoBroadcastsFound()
    {
        $fromDateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2010-01-15 06:00:00');
        $toDatetime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2017-10-16 06:00:00');
        $sid = new Sid('this_sid_doesnt_exist');

        $this->mockRepository->expects($this->once())
                             ->method('findAllByServiceAndDateRange')
                             ->with(
                                 $sid,
                                 $fromDateTime,
                                 $toDatetime,
                                 -1,
                                 0
                             )->willReturn([]);

        $broadcasts = $this->service()->findByServiceAndDateRange(
            $sid,
            $fromDateTime,
            $toDatetime,
            -1,
            1
        );

        $this->assertInternalType('array', $broadcasts);
        $this->assertCount(0, $broadcasts);
    }
}
