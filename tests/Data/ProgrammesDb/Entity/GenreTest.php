<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Genre;
use PHPUnit\Framework\TestCase;

class GenreTest extends TestCase
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
