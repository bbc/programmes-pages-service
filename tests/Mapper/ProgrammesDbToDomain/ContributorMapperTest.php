<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ContributorMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Contributor;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

class ContributorMapperTest extends PHPUnit_Framework_TestCase
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

        $mapper = new ContributorMapper();
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

        $mapper = new ContributorMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }
}
