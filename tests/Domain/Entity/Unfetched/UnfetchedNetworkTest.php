<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedNetwork;
use PHPUnit_Framework_TestCase;

class UnfetchedNetworkTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedNetwork()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Network',
            new UnfetchedNetwork()
        );
    }
}
