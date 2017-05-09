<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\RelatedLink;
use PHPUnit\Framework\TestCase;

class RelatedLinkTest extends TestCase
{
    public function testConstructor()
    {
        $relatedLink = new RelatedLink(
            'Title',
            'http://example.com',
            'Short Synopsis',
            'Longest Synopsis',
            'standard',
            true
        );

        $this->assertEquals('Title', $relatedLink->getTitle());
        $this->assertEquals('http://example.com', $relatedLink->getUri());
        $this->assertEquals('Short Synopsis', $relatedLink->getShortSynopsis());
        $this->assertEquals('Longest Synopsis', $relatedLink->getLongestSynopsis());
        $this->assertEquals('standard', $relatedLink->getType());
        $this->assertEquals(true, $relatedLink->isExternal());
    }
}
