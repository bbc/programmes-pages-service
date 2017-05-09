<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use PHPUnit\Framework\TestCase;

class UnfetchedMasterBrandTest extends TestCase
{
    public function testUnfetchedMasterBrand()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\MasterBrand',
            new UnfetchedMasterBrand()
        );
    }
}
