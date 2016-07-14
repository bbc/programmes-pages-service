<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindByIdsTest extends AbstractDatabaseTest
{
    public function testFindByIds()
    {
        $this->loadFixtures(['CategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');
        $dbId1 = $this->getCategoryDbId('C00018');
        $dbId2 = $this->getCategoryDbId('PT001');

        $entities = $repo->findByIds([$dbId1, $dbId2]);
        $this->assertSame(['C00018', 'PT001'], array_column($entities, 'pipId'));

        // findByIds query only
        $this->assertCount(1, $this->getDbQueries());
    }

    public function testFindByIdsFullWhenEmptyResult()
    {
        $this->loadFixtures(['CategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entities = $repo->findByIds([999]);
        $this->assertSame([], $entities);

        // findByIds query only
        $this->assertCount(1, $this->getDbQueries());
    }

    protected function getCategoryDbId($categoryPipId)
    {
        // Disable the logger for this call as we don't want to count it
        $this->getEntityManager()->getConfiguration()->getSQLLogger()->enabled = false;

        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Category');
        $id = $repo->findOneByPipId($categoryPipId)->getId();

        // Re enable the SQL logger
        $this->getEntityManager()->getConfiguration()->getSQLLogger()->enabled = true;

        return $id;
    }
}
