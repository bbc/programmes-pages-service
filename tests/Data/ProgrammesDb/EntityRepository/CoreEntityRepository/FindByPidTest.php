<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindByPidTest extends AbstractDatabaseTest
{
    public function testFindByPid()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPid('b00swyx1');
        $this->assertInternalType('array', $entity);
        $this->assertSame('b00swyx1', $entity['pid']);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByPidWhenEmptyResult()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findByPid('qqqqqqq');
        $this->assertNull($entity);

        // findByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
