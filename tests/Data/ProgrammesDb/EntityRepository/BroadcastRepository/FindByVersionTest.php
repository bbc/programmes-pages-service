<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository::<public>
 */
class FindByContributionToTest extends AbstractDatabaseTest
{
    public function testFindByVersion()
    {
        $this->loadFixtures(['BroadcastsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        foreach ($this->findByVersionData() as $data) {
            list($pids, $type, $limit, $offset, $repoToQuery, $expectedPids) = $data;

            $ids = array_map(function ($dbId) use ($repoToQuery) {

                return $this->getDbIdFromPid($dbId, $repoToQuery);
            }, $pids);

            $entities = $repo->findByVersion($ids, $type, $limit, $offset);

            $this->assertEquals($expectedPids, array_column($entities, 'pid'));

            // findByContributionTo query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findByVersionData()
    {
        return [
            [['v0000001'], 'Broadcast', 50, 0, 'Version', ['b0000001']],
            [['v0000004'], 'Broadcast', 50, 0, 'Version', ['b0000002']],
            [['v0000005'], 'Broadcast', 50, 0, 'Version', ['b0000003']],
            [['v0000006'], 'Webcast', 50, 0, 'Version', ['b0000004', 'b0000005']],
        ];
    }

    public function testFindByVersionWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Broadcast');

        $entities = $repo->findByVersion([1], 'Broadcast', 50, 0);
        $this->assertEquals([], $entities);

        // findByContributionTo query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
