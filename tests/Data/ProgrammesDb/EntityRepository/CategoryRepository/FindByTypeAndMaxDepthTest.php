<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository;

use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CategoryRepository::<public>
 */
class FindUsedByTypeTest extends AbstractDatabaseTest
{
    public function testFindAllByTypeAndMaxDepth()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        foreach ($this->findAllByTypeAndMaxDepthDataProvider() as $data) {
            [$type, $maxDepth, $expectedResult, $numQueries] = $data;

            $entities = $repo->findAllByTypeAndMaxDepth($type, $maxDepth);

            $this->assertSame($expectedResult, array_column($entities, 'pipId'));
            $this->assertCount($numQueries, $this->getDbQueries());

            $this->resetDbQueryLogger();
        }
    }

    public function findAllByTypeAndMaxDepthDataProvider()
    {
        return [
            ['genre', 2, ['C00193', 'C00196'], 2],
            ['genre', 1, ['C00193'], 1],
        ];
    }

    public function testFindUsedByTypeWhenEmptyResult()
    {
        $this->loadFixtures(['MongrelsWithCategoriesFixture']);
        $repo = $this->getRepository('ProgrammesPagesService:Category');

        $entities = $repo->findAllByTypeAndMaxDepth('thing', 3);
        $this->assertSame([], $entities);

        // findAlldByTypeAndMaxDepth query only
        $this->assertCount(1, $this->getDbQueries());
    }
}
