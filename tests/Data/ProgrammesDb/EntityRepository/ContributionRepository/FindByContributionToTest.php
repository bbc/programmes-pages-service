<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributionRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributionRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributionRepository::<public>
 */
class FindByContributionToTest extends AbstractDatabaseTest
{
    public function testFindByContributionTo()
    {
        $this->loadFixtures(['ContributionsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Contribution');

        foreach ($this->findByContributionToData() as $data) {
            list($pids, $type, $limit, $offset, $repoToQuery, $expectedPids) = $data;

            $ids = array_map(function ($dbId) use ($repoToQuery) {
                return $this->getDbIdFromPid($dbId, $repoToQuery);
            }, $pids);

            $entities = $repo->findByContributionTo($ids, $type, $limit, $offset);
            $this->assertEquals($expectedPids, array_column($entities, 'pid'));

            // findByContributionTo query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findByContributionToData()
    {
        return [
            [['v0000001'], 'version', 50, 0, 'Version', ['cntrbtn1', 'cntrbtn2']],
            [['v0000002'], 'version', 2, 1, 'Version', ['cntrbtn4']],
            [['b00swgkn'], 'programme', 50, 0, 'Programme', ['cntrbtn5']],
            [['sgmntms1'], 'segment', 50, 0, 'Segment', ['cntrbtn6']],
            [['sgmntms2'], 'segment', 50, 0, 'Segment', ['cntrbtn7']],
        ];
    }

    public function testFindByContributionToWhenEmptyResultSet()
    {
        $this->loadFixtures([]);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Contribution');

        $entities = $repo->findByContributionTo([1], 'version', 50, 0);
        $this->assertEquals([], $entities);

        // findByContributionTo query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
