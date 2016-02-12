<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

class CoreEntityRepositoryTest extends AbstractDatabaseTest
{
    public function testFindByPidFull()
    {
        $this->loadFixtures(['EastendersFixture']);
        $repo = $this->getEntityManager()->getRepository('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity');

        $entity = $repo->findByPidFull('b006m86d');

        $this->assertInternalType('array', $entity);
        $this->assertEquals('b006m86d', $entity['pid']);
    }
}
