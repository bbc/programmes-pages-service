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
            'entityId',
            'guid',
            'projectid',
            'admin',
            new DateTime(),
            new DateTime()
        );

        $this->assertSame([], $options->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTypeSetterThrowErrorWhenNoValidValue()
    {
        new RefOptions(
            'entityId',
            'guid',
            'projectid',
            'wrong type',
            new DateTime(),
            new DateTime()
        );
    }
}
