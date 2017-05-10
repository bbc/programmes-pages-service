<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\AtozTitle;
use PHPUnit\Framework\TestCase;

class AtozTitleTest extends TestCase
{
    public function testConstructorRequiredArgs()
    {
        $programme = $this->createMock('BBC\ProgrammesPagesService\Domain\Entity\Programme');

        $atozTitle = new AtozTitle('Title', 'T', $programme);

        $this->assertEquals('Title', $atozTitle->getTitle());
        $this->assertEquals('T', $atozTitle->getFirstLetter());
        $this->assertEquals($programme, $atozTitle->getTitledEntity());
    }
}
