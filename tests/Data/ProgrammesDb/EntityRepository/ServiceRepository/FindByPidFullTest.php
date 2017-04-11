<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository::<public>
 */
class FindByPidFullTest extends AbstractDatabaseTest
{
    public function testFindByPidFull()
    {
        $this->loadFixtures(['NetworksFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Service');

        $entity = $repo->findByPidFull('p00fzl7j');

        $this->assertInternalType('object', $entity);
        $this->assertEquals('p00fzl7j', $entity->getPid());
        $this->assertEquals('National Radio', $entity->getType());

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidFullWhenEmptyResult()
    {
        $this->loadFixtures(['NetworksFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Service');

        $entity = $repo->findByPidFull('qqqqqqq');
        $this->assertNull($entity);

        // findByPidFull query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
