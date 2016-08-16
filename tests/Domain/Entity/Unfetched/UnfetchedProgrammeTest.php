<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgramme;
use PHPUnit_Framework_TestCase;

class UnfetchedProgrammeTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedProgramme()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Programme',
            new UnfetchedProgramme()
        );
    }
}
