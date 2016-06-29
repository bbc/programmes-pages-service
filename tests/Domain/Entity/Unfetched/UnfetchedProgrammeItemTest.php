<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgrammeItem;
use PHPUnit_Framework_TestCase;

class UnfetchedProgrammeItemTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedProgrammeItem()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem',
            new UnfetchedProgrammeItem()
        );
    }
}
