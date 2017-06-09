<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefOptions;
use DateTime;
use PHPUnit\Framework\TestCase;

class RefOptionsTest extends TestCase
{
    public function testDefaults()
    {
        $options = new RefOptions(
            'guid',
            'projectid',
            'entityId',
            'admin'
        );

        $this->assertSame([], $options->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTypeSetterThrowErrorWhenNoValidValue()
    {
        new RefOptions(
            'guid',
            'entityId',
            'projectid',
            'wrong type'
        );
    }
}
