<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindIdByPidTest extends AbstractDatabaseTest
{
    public function testFindIdByPid()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');
        $expectedId = $this->getCoreEntityDbId('b00swyx1');

        $id = $repo->findIdByPid('b00swyx1');
        $this->assertSame($expectedId, $id);

        // findIdByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindIdByPidFullWhenEmptyResult()
    {
        $this->loadFixtures(['MongrelsFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $entity = $repo->findIdByPid('qqqqqqq');
        $this->assertNull($entity);

        // findIdByPid query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
