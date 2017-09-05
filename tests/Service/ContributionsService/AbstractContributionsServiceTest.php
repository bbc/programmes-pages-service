<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\ContributionsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractContributionsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('ContributionRepository');
        $this->setUpMapper('ContributionMapper', function ($dbContribution) {
            return $this->createConfiguredMock(Contribution::class, ['getPid' => new Pid($dbContribution['pid'])]);
        });
    }

    protected function service()
    {
        return new ContributionsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    protected function extractPids(array $contributions): array
    {
        return array_map(
            function ($contribution) {
                return (string) $contribution->getPid();
            },
            $contributions
        );
    }
}
