<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use PHPUnit\Framework\TestCase;

class UnfetchedNetworkTest extends TestCase
{
    public function testUnfetchedNetwork()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Network',
            new UnfetchedNetwork()
        );
    }
}
