<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use PHPUnit_Framework_TestCase;

class GenreTest extends PHPUnit_Framework_TestCase
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

    public function testConstructorWithParent()
    {
        $parentGenre = new Genre([0], 'parent_id', 'Parent Title', 'parent_url_key');
        $genre = new Genre([0, 1], 'id', 'Title', 'url_key', $parentGenre);

        $this->assertEquals(1, $genre->getDbId());
        $this->assertEquals([0, 1], $genre->getDbAncestryIds());
        $this->assertEquals('id', $genre->getId());
        $this->assertEquals('Title', $genre->getTitle());
        $this->assertEquals('url_key', $genre->getUrlKey());
        $this->assertEquals($parentGenre, $genre->getParent());
    }
}
