<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperProvider;
use PHPUnit_Framework_TestCase;

class MapperProviderTest extends PHPUnit_Framework_TestCase
{
    const MAPPER_NS = 'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\';

    public function testGetters()
    {
        $mp = new MapperProvider();
        $this->assertInstanceOf(self::MAPPER_NS . 'CategoryMapper', $mp->getCategoryMapper());
        $this->assertInstanceOf(self::MAPPER_NS . 'ImageMapper', $mp->getImageMapper());
        $this->assertInstanceOf(self::MAPPER_NS . 'MasterBrandMapper', $mp->getMasterBrandMapper());
        $this->assertInstanceOf(self::MAPPER_NS . 'NetworkMapper', $mp->getNetworkMapper());
        $this->assertInstanceOf(self::MAPPER_NS . 'ProgrammeMapper', $mp->getProgrammeMapper());
        $this->assertInstanceOf(self::MAPPER_NS . 'RelatedLinkMapper', $mp->getRelatedLinkMapper());
        $this->assertInstanceOf(self::MAPPER_NS . 'ServiceMapper', $mp->getServiceMapper());
        $this->assertInstanceOf(self::MAPPER_NS . 'VersionMapper', $mp->getVersionMapper());
    }
}
