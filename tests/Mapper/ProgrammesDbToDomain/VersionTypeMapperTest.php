<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\VersionTypeMapper;
use BBC\ProgrammesPagesService\Domain\Entity\VersionType;

class VersionTypeMapperTest extends BaseMapperTestCase
{
    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => '1',
            'name' => 'Original version',
            'type' => 'Original',
        ];

        $expectedEntity = new VersionType('Original', 'Original version');

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    private function getMapper(): VersionTypeMapper
    {
        return new VersionTypeMapper($this->getMapperFactory());
    }
}
