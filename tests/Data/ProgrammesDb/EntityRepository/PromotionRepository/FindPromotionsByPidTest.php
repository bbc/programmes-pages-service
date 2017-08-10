<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use Tests\BBC\ProgrammesPagesService\AbstractDatabaseTest;

/**
 * @covers BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\PromotionRepository::<public>
 */
class FindPromotionsByPidTest extends AbstractDatabaseTest
{
    /**
     * @group dev
     */
    public function testNoAdditionalQueriesWhenNoCategoriesAreFound()
    {
        $this->loadFixtures(['MongrelsFixture']);

        $repo = $this->getRepository('ProgrammesPagesService:Promotion');

        $pid = new Pid('b010t19z');
        $entity = $repo->findActivePromotionsByPid($pid, 300, 0);
    }
}
