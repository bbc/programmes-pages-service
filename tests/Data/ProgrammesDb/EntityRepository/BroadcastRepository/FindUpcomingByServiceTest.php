<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindUpcomingByServiceTest extends AbstractDatabaseTest
{
    /** @var  BroadcastRepository */
    private $repo;

    public function setUp()
    {
        $this->loadFixtures(['BroadcastsFixture']);
        $this->repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');
    }

    public function testFindUpcomingByService()
    {
        $serviceId = $this->getDbIdFromPersistentIdentifier('p00fzl8v', 'Service');

        $cutoffDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2011-08-05 16:25:00');

        $broadcasts = $this->repo->findUpcomingByService(
            $serviceId,
            'Broadcast',
            $cutoffDate,
            null,
            0
        );

        $this->assertInternalType('array', $broadcasts);
        $this->assertCount(2, $broadcasts);

        $this->assertEquals(
            ['b0000006', 'b0000007'],
            array_column($broadcasts, 'pid')
        );

        $this->assertEquals(new DateTimeImmutable('2011-09-05 15:00:00'), $broadcasts[0]['startAt']);
        $this->assertEquals('p0000001', $broadcasts[0]['programmeItem']['pid']);
        $this->assertEquals('1', $broadcasts[0]['programmeItem']['tleoId']);


        // broadcasts + ?programmeItem parent
        $this->assertCount(1, $this->getDbQueries());
    }


    public function testFindUpcomingByServiceWithParents()
    {
        $serviceId = $this->getDbIdFromPersistentIdentifier('p00fzl8v', 'Service');

        $cutoffDate = DateTimeImmutable::createFromFormat('Y-m-d H:i:s', '2011-08-04 14:00:00');

        $broadcasts = $this->repo->findUpcomingByService(
            $serviceId,
            'Broadcast',
            $cutoffDate,
            null,
            0
        );

        $this->assertInternalType('array', $broadcasts);
        $this->assertCount(3, $broadcasts);

        $this->assertEquals(
            ['b0000003', 'b0000006', 'b0000007'],
            array_column($broadcasts, 'pid')
        );

        $this->assertEquals(new DateTimeImmutable('2011-08-05 15:00:00'), $broadcasts[0]['startAt']);
        $this->assertEquals('p0000003', $broadcasts[0]['programmeItem']['pid']);
        $this->assertEquals('7', $broadcasts[0]['programmeItem']['tleoId']);

        // broadcasts + programmeItem parent
        $this->assertCount(2, $this->getDbQueries());
    }



    public function testFindUpcomingByServiceWhenEmptyResultSet()
    {
        $serviceId = $this->getDbIdFromPersistentIdentifier('p00fzl8v', 'Service');

        $broadcasts = $this->repo->findUpcomingByService(
            $serviceId,
            'Broadcast',
            new DateTimeImmutable('3000-10-16 00:00:00'),
            1,
            0
        );

        $this->assertSame([], $broadcasts);
    }
}
