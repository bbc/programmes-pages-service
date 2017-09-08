<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributionsService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributionRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Contribution;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributionMapper;
use BBC\ProgrammesPagesService\Service\ContributionsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractContributionsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo(ContributionRepository::class);
        $this->setUpMapper(ContributionMapper::class, function ($dbContribution) {
            return $this->createConfiguredMock(Contribution::class, ['getPid' => new Pid($dbContribution['pid'])]);
        });
    }

    protected function service()
    {
        return new ContributionsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
