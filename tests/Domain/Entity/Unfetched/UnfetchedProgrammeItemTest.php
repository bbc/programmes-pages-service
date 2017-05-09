<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedProgrammeItem;
use PHPUnit\Framework\TestCase;

class UnfetchedProgrammeItemTest extends TestCase
{
    public function testUnfetchedProgrammeItem()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\ProgrammeItem',
            new UnfetchedProgrammeItem()
        );
    }
}
