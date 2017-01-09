<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\RelatedLinkMapper;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;

class RelatedLinkMapperTest extends BaseMapperTestCase
{
    public function testGetDomainModel()
    {
        $dbEntityArray = [
            'id' => '1',
            'pid' => 'p03slh3p',
            'title' => 'Title',
            'uri' => 'http://example.com/',
            'shortSynopsis' => 'ShortSynopsis',
            'mediumSynopsis' => 'MediumSynopsis',
            'longSynopsis' => 'LongestSynopsis',
            'type' => 'standard',
            'isExternal' => true,
            'position' => 1,
        ];

        $expectedEntity = new RelatedLink(
            'Title',
            'http://example.com/',
            'ShortSynopsis',
            'LongestSynopsis',
            'standard',
            true
        );

        $mapper = $this->getMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));

        // Requesting the same entity multiple times reuses a cached instance
        // of the entity, rather than creating a new one every time
        $this->assertSame(
            $mapper->getDomainModel($dbEntityArray),
            $mapper->getDomainModel($dbEntityArray)
        );
    }

    private function getMapper(): RelatedLinkMapper
    {
        return new RelatedLinkMapper($this->getMapperFactory());
    }
}
