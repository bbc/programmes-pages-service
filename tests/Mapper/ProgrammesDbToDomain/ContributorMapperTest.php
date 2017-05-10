<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributorMapper;

class ContributorMapperTest extends BaseMapperTestCase
{
    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p01v0q3w',
            'type' => 'person',
            'name' => 'Peter Capaldi',
            'sortName' => 'Capaldi, Peter',
            'givenName' => null,
            'familyName' => null,
            'musicBrainzId' => null,
        ];

        $pid = new Pid('p01v0q3w');
        $expectedEntity = new Contributor(1, $pid, 'person', 'Peter Capaldi', 'Capaldi, Peter');

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    public function testGetDomainModelOptionals()
    {
        $dbEntityArray = [
            'id' => 1,
            'pid' => 'p01v0q3w',
            'type' => 'person',
            'name' => 'Peter Capaldi',
            'sortName' => 'Capaldi, Peter',
            'givenName' => 'Peter',
            'familyName' => 'Capaldi',
            'musicBrainzId' => '5df5318d-4af6-4349-afc2-7391f092e9e2',
        ];

        $pid = new Pid('p01v0q3w');

        $expectedEntity = new Contributor(
            1,
            $pid,
            'person',
            'Peter Capaldi',
            'Capaldi, Peter',
            'Peter',
            'Capaldi',
            '5df5318d-4af6-4349-afc2-7391f092e9e2'
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }

    private function getMapper(): ContributorMapper
    {
        return new ContributorMapper($this->getMapperFactory());
    }
}
