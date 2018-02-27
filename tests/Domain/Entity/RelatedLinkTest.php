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
            'standard'
        );

        $this->assertEquals('Title', $relatedLink->getTitle());
        $this->assertEquals('http://example.com', $relatedLink->getUri());
        $this->assertEquals('Short Synopsis', $relatedLink->getShortSynopsis());
        $this->assertEquals('Longest Synopsis', $relatedLink->getLongestSynopsis());
        $this->assertEquals('standard', $relatedLink->getType());
        $this->assertEquals(true, $relatedLink->isExternal());
        $this->assertEquals('example.com', $relatedLink->getHost());
    }

    /**
     * @dataProvider isExternalDataProvider
     */
    public function testIsExternal($uri, $expectedHost, $expectedIsExternal)
    {
        $relatedLink = new RelatedLink(
            'Title',
            $uri,
            'Short Synopsis',
            'Longest Synopsis',
            'standard'
        );

        $this->assertEquals($expectedIsExternal, $relatedLink->isExternal());
        $this->assertEquals($expectedHost, $relatedLink->getHost());
    }

    public function isExternalDataProvider()
    {
        return [
            ['https://e.co/foo', 'e.co', true],
            ['https://e.co?bar', 'e.co', true],
            ['http://bbc.co.uk/foo', 'bbc.co.uk', false],
            ['http://bbc.co.uk?foo', 'bbc.co.uk', false],
            ['http://bbc.com/foo', 'bbc.com', false],
            ['http://bbc.com?foo', 'bbc.com', false],

            // Subdomains of bbc.co.uk / bbc.com count as internal links
            ['http://www.bbc.co.uk/foo', 'www.bbc.co.uk', false],
            ['https://brap.bbc.co.uk/foo', 'brap.bbc.co.uk', false],

            // It should match the host, not things that look like a host in
            // the path or querystring
            ['http://bbc.com/http://e.co', 'bbc.com', false],
            ['https://bbc.co.uk?https://e.co', 'bbc.co.uk', false],

            // bbc.co.uk / bbc.com must be appear at the end of the host
            ['http://www.bbc.co.uk.nope/foo', 'www.bbc.co.uk.nope', true],
            ['https://brap.bbc.co.uk.fake/foo', 'brap.bbc.co.uk.fake', true],

            // Invalid hosts should be considered internal
            ['' , '', false],
            ['foo', '', false],
        ];
    }
}
