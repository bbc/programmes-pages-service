<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGenre;
use PHPUnit_Framework_TestCase;

class UnfetchedGenreTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedGenre()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Genre',
            new UnfetchedGenre()
        );
    }
}
