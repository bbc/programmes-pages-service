<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\ContributionsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractContributionsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('ContributionRepository');
        $this->setUpMapper('ContributionMapper', 'contributionFromDbData');
    }

    protected function contributionsFromDbData(array $entities)
    {
        return array_map([$this, 'contributionFromDbData'], $entities);
    }

    protected function contributionFromDbData(array $entity)
    {
        $mockContribution = $this->createMock(self::ENTITY_NS . 'Contribution');

        $mockContribution->method('getPid')->willReturn($entity['pid']);
        return $mockContribution;
    }

    protected function service()
    {
        return new ContributionsService($this->mockRepository, $this->mockMapper);
    }
}
