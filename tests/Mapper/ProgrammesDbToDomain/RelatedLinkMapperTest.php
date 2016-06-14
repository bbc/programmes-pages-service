<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\RelatedLinkMapper;
use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use PHPUnit_Framework_TestCase;

class RelatedLinkMapperTest extends PHPUnit_Framework_TestCase
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

        $mapper = new RelatedLinkMapper();
        $this->assertEquals($expectedEntity, $mapper->getDomainModel($dbEntityArray));
    }
}
