<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Genre;
use PHPUnit_Framework_TestCase;

class GenreTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $genre = new Genre('Title', 'url_key');

        $this->assertEquals('Title', $genre->getTitle());
        $this->assertEquals('url_key', $genre->getUrlKey());
    }

    public function testConstructorWithParent()
    {
        $parentGenre = new Genre('Parent Title', 'parent_url_key');
        $genre = new Genre('Title', 'url_key', $parentGenre);

        $this->assertEquals('Title', $genre->getTitle());
        $this->assertEquals('url_key', $genre->getUrlKey());
        $this->assertEquals($parentGenre, $genre->getParent());
    }
}
