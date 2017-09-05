<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Service\ContributionsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractContributionsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('ContributionRepository');
        $this->setUpMapper('ContributionMapper', function ($dbContribution) {
            return $this->createConfiguredMock(Contribution::class,
                ['getPid' => $dbContribution['pid']]
            );
        });
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
        return new ContributionsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
