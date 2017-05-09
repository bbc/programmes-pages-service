<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use PHPUnit\Framework\TestCase;

class UnfetchedServiceTest extends TestCase
{
    public function testUnfetchedService()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Service',
            new UnfetchedService()
        );
    }
}
