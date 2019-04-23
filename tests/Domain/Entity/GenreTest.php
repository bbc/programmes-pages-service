<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGenre;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
{
    public function testConstructor()
    {
        $genre = new Genre([0], 'id', 'Title', 'url_key');

        $this->assertEquals(0, $genre->getDbId());
        $this->assertEquals([0], $genre->getDbAncestryIds());
        $this->assertEquals('id', $genre->getId());
        $this->assertEquals('Title', $genre->getTitle());
        $this->assertEquals('url_key', $genre->getUrlKey());
    }

    public function testConstructorWithOptionalArgs()
    {
        $parentGenre = new Genre([0], 'parent_id', 'Parent Title', 'parent_url_key', null);
        $genre = new Genre([0, 1], 'id', 'Title', 'url_key', $parentGenre);

        $this->assertEquals(1, $genre->getDbId());
        $this->assertEquals([0, 1], $genre->getDbAncestryIds());
        $this->assertEquals('id', $genre->getId());
        $this->assertEquals('Title', $genre->getTitle());
        $this->assertEquals('url_key', $genre->getUrlKey());
        $parent = $genre->getParent();
        $this->assertEquals($parentGenre, $parent);
        if ($parent) {
            $this->assertNull($parent->getParent());
        }
    }

     /**
     * @expectedException \BBC\ProgrammesPagesService\Domain\Exception\DataNotFetchedException
     * @expectedExceptionMessage Could not get Parent of Genre "id" as it was not fetched
     */
    public function testRequestingUnfetchedParentThrowsException()
    {
        $parentGenre = new UnfetchedGenre();
        $genre = new Genre([0], 'id', 'Title', 'url_key', $parentGenre);

        $genre->getParent();
    }

    public function testGetAncestryWithParent()
    {
        $parentGenre = new Genre([0], 'parent_id', 'Parent Title', 'parent_url_key', null);
        $genre = new Genre([0, 1], 'id', 'Title', 'url_key', $parentGenre);

        $ancestry = $genre->getAncestry();

        $this->assertEquals([$parentGenre, $genre], $ancestry);
    }

    public function testGetAncestryWithNoParent()
    {
        $genre = new Genre([0], 'id', 'Title', 'url_key', null);
        $ancestry = $genre->getAncestry();

        $this->assertEquals([$genre], $ancestry);
    }

    public function testGetUrlKeyHierarchyWithParent()
    {
        $parentGenre = new Genre([0], 'parent_id', 'Parent Title', 'parent_url_key', null);
        $genre = new Genre([0, 1], 'id', 'Title', 'url_key', $parentGenre);

        $urlKey = $genre->getUrlKeyHierarchy();

        $this->assertEquals("parent_url_key/url_key", $urlKey);
    }

    public function testGetUrlKeyHierarchyWithNoParent()
    {
        $genre = new Genre([0], 'id', 'Title', 'url_key', null);

        $urlKey = $genre->getUrlKeyHierarchy();

        $this->assertEquals("url_key", $urlKey);
    }
}
