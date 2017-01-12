<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedMasterBrand;
use PHPUnit_Framework_TestCase;

class UnfetchedMasterBrandTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedMasterBrand()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\MasterBrand',
            new UnfetchedMasterBrand()
        );
    }
}
