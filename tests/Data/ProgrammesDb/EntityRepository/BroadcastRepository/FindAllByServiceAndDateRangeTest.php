<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Domain\ValueObject\Sid;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindAllByServiceAndDateRangeTest extends AbstractDatabaseTest
{
    /** @var  BroadcastRepository */
    private $repo;

    public function setUp()
    {
        $this->loadFixtures(['BroadcastsFixture']);
        $this->repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');
    }

    public function testFindAllByServiceAndDateRange()
    {
        $fromDateTime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2010-01-15 06:00:00');
        $toDatetime = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2017-10-16 06:00:00');

        $broadcasts = $this->repo->findAllByServiceAndDateRange(
            new Sid('bbc_radio_two'),
            $fromDateTime,
            $toDatetime,
            null,
            0
        );


        $this->assertInternalType('array', $broadcasts);
        $this->assertCount(3, $broadcasts);
        $this->assertEquals(
            ['b0000003', 'b0000006', 'b0000007'],
            array_column($broadcasts, 'pid')
        );


        $this->assertEquals('Programme Image', $broadcasts[0]['programmeItem']['image']['title']);
        $this->assertEquals('Network Image', $broadcasts[1]['programmeItem']['masterBrand']['network']['image']['title']);

        // broadcasts + programmeItem parent
        $this->assertCount(2, $this->getDbQueries());
    }

    public function testFindAllByServiceAndDateRangeWhenEmptyResultSet()
    {
        $fromDateTime = new DateTimeImmutable('2010-01-15 06:00:00');
        $toDatetime = new DateTimeImmutable('2017-10-16 06:00:00');

        $broadcastedServices = $this->repo->findAllByServiceAndDateRange(
            new Sid('this_sid_doesnt_exist'),
            $fromDateTime,
            $toDatetime,
            null,
            0
        );

        $this->assertSame([], $broadcastedServices);
    }
}
