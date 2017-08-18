<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefIsiteOptions;
use DateTime;
use PHPUnit\Framework\TestCase;

class RefIsiteOptionsTest extends TestCase
{
    public function testDefaults()
    {
        $options = new RefIsiteOptions(
            'guid',
            'projectid',
            'parentProjectid',
            'entityId',
            'fileId',
            'admin',
            new DateTime('U'),
            new DateTime('U')
        );

        $this->assertSame([], $options->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTypeSetterThrowErrorWhenNoValidValue()
    {
        new RefIsiteOptions(
            'guid',
            'projectid',
            'parentProjectid',
            'entityId',
            'fileId',
            'wrong type',
            new DateTime('U'),
            new DateTime('U')
        );
    }
}
