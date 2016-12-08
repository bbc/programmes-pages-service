<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\AtoZTitle;
use InvalidArgumentException;
use PHPUnit_Framework_TestCase;

class AtoZTitleTest extends PHPUnit_Framework_TestCase
{
    public function testConstructorRequiredArgs()
    {
        $programme = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Programme');

        $atozTitle = new AtoZTitle('Title', 'T', $programme);

        $this->assertEquals('Title', $atozTitle->getTitle());
        $this->assertEquals('T', $atozTitle->getFirstLetter());
        $this->assertEquals($programme, $atozTitle->getCoreEntity());
    }
}
