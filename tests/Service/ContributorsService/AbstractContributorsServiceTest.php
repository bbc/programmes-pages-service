<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributorsService;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ContributorRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributorMapper;
use BBC\ProgrammesPagesService\Service\ContributorsService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractContributorsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo(ContributorRepository::class);
        $this->setUpMapper(ContributorMapper::class, function ($dbContributor) {
            return $this->createConfiguredMock(Contributor::class, ['getMusicBrainzId' => $dbContributor['musicBrainzId']]);
        });
    }

    protected function service()
    {
        return new ContributorsService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }
}
