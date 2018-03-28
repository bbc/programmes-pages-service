<?php

namespace Tests\BBC\ProgrammesPagesService\Service\GroupsService;

use BBC\ProgrammesPagesService\Domain\Entity\Group;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;

class FindByCoreEntityMembershipTest extends AbstractGroupsServiceTest
{
    public function testGroupsCanBeReceived()
    {
        $this->mockRepository->method('findByCoreEntityMembership')->willReturn([['pid' => 'b010t19z']]);

        $groups = $this->service()->findByCoreEntityMembership($this->createMock(Programme::class));

        $this->assertCount(1, $groups);
        $this->assertContainsOnlyInstancesOf(Group::class, $groups);
        $this->assertEquals('b010t19z', (string) $groups[0]->getPid());
    }

    public function testNullValueIsReceivedWhenNoResults()
    {
        $this->mockRepository->method('findByCoreEntityMembership')->willReturn([]);

        $groups = $this->service()->findByCoreEntityMembership($this->createMock(Programme::class));

        $this->assertEmpty($groups);
    }
}
