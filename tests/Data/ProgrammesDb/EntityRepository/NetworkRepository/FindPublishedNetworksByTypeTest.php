<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\NetworkRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\NetworkRepository::<public>
 */
class FindPublishedNetworksByTypeTest extends AbstractDatabaseTest
{
    public function testFindPublishedNetworksByType()
    {
        $this->loadFixtures(['NetworksFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Network');

        foreach ($this->findPublishedNetworksByTypeData() as $data) {
            list($types, $limit, $offset, $expectedPids) = $data;

            $entities = $repo->findPublishedNetworksByType($types, $limit, $offset);
            $this->assertEquals($expectedPids, array_column($entities, 'nid'));

            // FindPublishedNetworksByType query only
            $this->assertCount(1, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findPublishedNetworksByTypeData()
    {
        return [
            [['National Radio'], 50, 0, ['bbc_radio_two', 'bbc_radio_four']],
            [['National Radio'], 2, 1, ['bbc_radio_four']],
            [['TV'],  50, 0, ['bbc_one']],
            // Empty Result
            [['Garbage Type'], 50, 0 , []],
        ];
    }
}
