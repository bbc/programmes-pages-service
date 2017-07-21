<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;
use PHPUnit\Framework\TestCase;

class MapperFactoryTest extends TestCase
{
    const MAPPER_NS = 'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\';

    /**
     * @dataProvider mapperNamesDataProvider
     */
    public function testGetters($mapperName)
    {
        $mapperFactory = new MapperFactory();

        $mapper = $mapperFactory->{'get' . $mapperName}();

        // Assert it returns an instance of the correct class
        $this->assertInstanceOf(self::MAPPER_NS . $mapperName, $mapper);

        // Requesting the same mapper multiple times reuses the same instance of
        // a mapper, rather than creating a new one every time
        $this->assertSame($mapper, $mapperFactory->{'get' . $mapperName}());
    }

    public function mapperNamesDataProvider()
    {
        return [
            ['AtozTitleMapper'],
            ['BroadcastMapper'],
            ['CategoryMapper'],
            ['CollapsedBroadcastMapper'],
            ['ContributionMapper'],
            ['ContributorMapper'],
            ['GroupMapper'],
            ['ImageMapper'],
            ['MasterBrandMapper'],
            ['NetworkMapper'],
            ['OptionsMapper'],
            ['ProgrammeMapper'],
            ['RelatedLinkMapper'],
            ['SegmentMapper'],
            ['SegmentEventMapper'],
            ['ServiceMapper'],
            ['VersionMapper'],
            ['VersionTypeMapper'],
        ];
    }
}
