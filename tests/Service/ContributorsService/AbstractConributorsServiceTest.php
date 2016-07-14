<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ContributorsService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\ProgrammesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractContributorsServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpRepo('ContributorRepository');
        $this->setUpMapper('ContributorMapper', 'contributorFromDbData');
    }

    protected function contributorsFromDbData(array $entities)
    {
        return array_map([$this, 'contributorFromDbData'], $entities);
    }

    protected function contributorFromDbData(array $entity)
    {
        $mockContributor = $this->createMock(self::ENTITY_NS . 'Contributor');

        $mockContributor->method('getMusicBrainzId')->willReturn($entity['musicBrainzId']);
        return $mockContributor;
    }

    protected function service()
    {
        return new ProgrammesService($this->mockRepository, $this->mockMapper);
    }
}
