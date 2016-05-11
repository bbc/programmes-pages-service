<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PipsBackfillRepository;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PipsBackfillRepository;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

class PipsBackfillRepositoryTest extends AbstractDatabaseTest
{
    public function testLockingWorks()
    {
        $this->loadFixtures(['PipsBackfillFixture']);
        /** @var PipsBackfillRepository $repo */
        $repo = $this->getEntityManager()->getRepository('ProgrammesPagesService:PipsBackfill');
        // Can't really test concurrency in unit tests, but we can assert we're not totally up
        // shit creek without a paddle
        $firstItems = $repo->findAndLockOldestUnprocessedItems(2);
        $secondItems = $repo->findAndLockOldestUnprocessedItems(2);
        $thirdItems = $repo->findAndLockOldestUnprocessedItems(2);

        $this->assertCount(2, $firstItems);
        $this->assertCount(2, $secondItems);
        $this->assertEmpty($thirdItems);
        $this->assertEquals('b006m86d', $firstItems[0]->getEntityId());
        $this->assertEquals('b0000000', $secondItems[0]->getEntityId());

        foreach ($firstItems as $item) {
            $repo->unlock($item);
        }
        $firstItemsAgain = $repo->findAndLockOldestUnprocessedItems(2);
        $this->assertCount(2, $firstItemsAgain);
        $this->assertEquals('b006m86d', $firstItemsAgain[0]->getEntityId());
    }
}
