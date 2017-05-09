<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity\Unfetched;

use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedImage;
use PHPUnit\Framework\TestCase;

class UnfetchedImageTest extends TestCase
{
    public function testUnfetchedImage()
    {
        $this->assertInstanceOf(
            'BBC\ProgrammesPagesService\Domain\Entity\Image',
            new UnfetchedImage()
        );
    }
}
