<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperProvider;
use PHPUnit_Framework_TestCase;

class MapperProviderTest extends PHPUnit_Framework_TestCase
{
    const MAPPER_NS = 'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\';

    /**
     * @dataProvider mapperNamesDataProvider
     */
    public function testGetters($mapperName)
    {
        $mapperProvider = new MapperProvider();

        $mapper = $mapperProvider->{'get' . $mapperName}();

        // Assert it returns an instance of the correct class
        $this->assertInstanceOf(self::MAPPER_NS . $mapperName, $mapper);

        // Requesting the same mapper multiple times reuses the same instance of
        // a mapper, rather than creating a new one every time
        $this->assertSame($mapper, $mapperProvider->{'get' . $mapperName}());
    }

    public function mapperNamesDataProvider()
    {
        return [
            ['CategoryMapper'],
            ['ImageMapper'],
            ['MasterBrandMapper'],
            ['NetworkMapper'],
            ['ProgrammeMapper'],
            ['RelatedLinkMapper'],
            ['ServiceMapper'],
            ['VersionMapper'],
        ];
    }
}
