<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\NetworkRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\NetworkRepository::<public>
 */
class FindByUrlKeyWithDefaultServiceTest extends AbstractDatabaseTest
{
    public function testFindByUrlKey()
    {
        $this->loadFixtures(['NetworksFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Network');

        $entity = $repo->findByUrlKeyWithDefaultService('radio4');
        $this->assertInternalType('array', $entity);
        $this->assertEquals('bbc_radio_four', $entity['nid']);
        $this->assertEquals('bbc_radio_fourfm', $entity['defaultService']['sid']);

        // must have only been one query (including the join)
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByUrlKeyWhenEmptyResult()
    {
        $this->loadFixtures(['NetworksFixture']);
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Network');

        $entity = $repo->findByUrlKeyWithDefaultService('radionope');
        $this->assertNull($entity);

        // single query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
