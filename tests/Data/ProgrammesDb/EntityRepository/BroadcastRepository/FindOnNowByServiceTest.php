<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use DateTimeImmutable;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindOnNowByServiceTest extends AbstractDatabaseTest
{
    /** @var  BroadcastRepository */
    private $repo;

    public function setUp()
    {
        $this->loadFixtures(['BroadcastsFixture']);
        $this->repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');
    }

    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    /**
     * @dataProvider broadcastDataProvider
     */
    public function testFindOnNowByServiceRepositoryFetch(
        $cutoffDateTime,
        $expectedPid,
        $expectedProgrammeItemId,
        $expectedTleoId,
        $expectedDbQueries
    ) {
        $serviceId = $this->getDbIdFromPersistentIdentifier('p00fzl8v', 'Service');

        $broadcast = $this->repo->findOnNowByService(
            $serviceId,
            'Broadcast',
            $cutoffDateTime
        );

        $this->assertInternalType('array', $broadcast);
        $this->assertEquals($expectedPid, $broadcast['pid']);
        $this->assertEquals($expectedProgrammeItemId, $broadcast['programmeItem']['pid']);
        $this->assertEquals($expectedTleoId, $broadcast['programmeItem']['tleoId']);
        $this->assertCount($expectedDbQueries, $this->getDbQueries());
    }

    public function broadcastDataProvider(): array
    {
        return [
            // [cutoffDateTime, expectedPid, expectedProgrammeItemId, expectedTleoId, expectedDbQueries]
            [new DateTimeImmutable('2011-08-05 15:20:00'), 'b0000003', 'p0000003', 9, 2],
            [new DateTimeImmutable('2011-09-05 15:00:00'), 'b0000006', 'p0000001', 1, 1],
            [new DateTimeImmutable('2011-09-05 16:00:00'), 'b0000006', 'p0000001', 1, 1],
        ];
    }

    public function testFindOnNowByServiceRepositoryFetchEmptyResultsSet()
    {
        $serviceId = $this->getDbIdFromPersistentIdentifier('p00fzl8v', 'Service');

        $broadcast = $this->repo->findOnNowByService(
            $serviceId,
            'Broadcast',
            new DateTimeImmutable('3000-08-05 13:00:00')
        );

        $this->assertSame(null, $broadcast);
    }
}
