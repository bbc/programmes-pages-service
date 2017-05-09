<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedGenre;
use PHPUnit\Framework\TestCase;

class UnfetchedGenreTest extends TestCase
{
    public function testUnfetchedGenre()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Genre',
            new UnfetchedGenre()
        );
    }
}
