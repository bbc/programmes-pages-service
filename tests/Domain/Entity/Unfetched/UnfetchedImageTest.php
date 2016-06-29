<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedImage;
use PHPUnit_Framework_TestCase;

class UnfetchedImageTest extends PHPUnit_Framework_TestCase
{
    public function testUnfetchedImage()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Image',
            new UnfetchedImage()
        );
    }
}
