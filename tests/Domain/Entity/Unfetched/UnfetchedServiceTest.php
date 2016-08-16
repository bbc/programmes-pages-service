<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use PHPUnit_Framework_TestCase;

class UnfetchedServiceTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedService()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Service',
            new UnfetchedService()
        );
    }
}
