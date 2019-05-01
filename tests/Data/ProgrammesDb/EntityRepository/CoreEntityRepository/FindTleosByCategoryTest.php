<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository::<public>
 */
class FindTleosByCategoryTest extends AbstractDatabaseTest
{
    public function setUp()
    {
        /**
         * From fixture:
         *
         *  brand1
         *      cat1/cat11          (category) - /1/2
         *      form1               (format) - /4
         *      streamable=True     (availability)
         *
         *  brand2
         *      cat1/cat11/cat111   (category) - /1/2/3
         *      form2               (format) - /5
         *      streamable=False    (availability)
         */
        $this->loadFixtures(['TleosByCategoryFixture']);
    }

    public function tearDown()
    {
        $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity')->clearAncestryCache();
    }

    public function testFindTleosByCategoryAllAvailabilityInCategory()
    {
        $ids = $this->findCategoryAndChildIds('C00925', 'genre');

        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $selectedAvailabilityForTleos = false;

        $tleos = $repo->findTleosByCategories(
            $ids,
            $selectedAvailabilityForTleos,
            true,
            null,
            0
        );

        $this->assertInternalType('array', $tleos);
        $this->assertCount(1, $tleos);

        $this->assertEquals('Brand2', $tleos[0]['title']);
    }

    public function testFindTleosByCategoryAllAvailability()
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $selectedCategoryForTleos = $this->findCategoryAndChildIds('C00124', 'genre'); // /cat1/cat11
        $selectSpecificAvailavility = false;

        $tleos = $repo->findTleosByCategories(
            $selectedCategoryForTleos,
            $selectSpecificAvailavility,
            true,
            null,
            0
        );

        $this->assertInternalType('array', $tleos);
        $this->assertCount(2, $tleos);

        $this->assertEquals('Brand2', $tleos[0]['title']);
        $this->assertEquals('Brand1', $tleos[1]['title']);
    }

    public function testFindTleosByCategoryPlayerAvailability()
    {
        /** @var CoreEntityRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:CoreEntity');

        $selectedCategoryForTleos = $this->findCategoryAndChildIds('C00124', 'genre'); // /cat1/cat11
        $selectedAvailabilityForTleos = true;

        $tleos = $repo->findTleosByCategories(
            $selectedCategoryForTleos,
            $selectedAvailabilityForTleos,
            true,
            null,
            0
        );

        $this->assertInternalType('array', $tleos);
        $this->assertCount(1, $tleos);

        $this->assertEquals('Brand1', $tleos[0]['title']);
    }

    private function findCategoryAndChildIds(string $pipId, string $type)
    {
        $id = $this->getDbIdFromPersistentIdentifier($pipId, 'Category', 'pipId');
        $categoryRepo = $this->getEntityManager()->getRepository('ProgrammesPagesService:Category');
        $category = $categoryRepo->findByIdWithAllDescendants($id, $type);

        $ids = [$category['id']];
        foreach ($category['children'] as $childCategory) {
            $ids[] = $childCategory['id'];
        }
        return $ids;
    }
}
