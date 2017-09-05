<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributorsService;

use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Service\ContributorsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractContributorsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('ContributorRepository');

        $this->setUpMapper('ContributorMapper', function ($dbContributor) {
            return $this->createMock(Contributor::class);
        });
    }

    protected function service()
    {
        return new ContributorsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
