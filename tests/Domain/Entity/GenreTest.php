<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre;
use PHPUnit_Framework_TestCase;

class GenreTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $genre = new Genre('P0006', 'Wibble', 'wibble');
        $parentGenre = new Genre('P005', 'Bibble', 'bibble');
        $genre->setParent($parentGenre);
        $this->assertEquals('P0006', $genre->getPipId());
        $this->assertEquals('Wibble', $genre->getTitle());
        $this->assertEquals('wibble', $genre->getUrlKey());
        $this->assertEquals($parentGenre, $genre->getParent());
    }
}
