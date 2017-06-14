<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Clip;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefOptions;
use DateTime;
use PHPUnit\Framework\TestCase;

class RefOptionsTest extends TestCase
{
    public function testDefaults()
    {
        $entity = new Clip('pid', 'title');
        $createdAt = new DateTime("U");
        $modifiedAt = new DateTime("U");

        $options = new RefOptions(
            'guid',
            'projectid',
            $entity,
            'admin',
            $createdAt,
            $modifiedAt
        );

        $this->assertSame([], $options->getOptions());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testTypeSetterThrowErrorWhenNoValidValue()
    {
        $entity = new Clip('pid', 'title');
        $createdAt = new DateTime("U");
        $modifiedAt = new DateTime("U");

        new RefOptions(
            'guid',
            'projectid',
            $entity,
            'other type',
            $createdAt,
            $modifiedAt
        );
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testSetInvalidEntity()
    {
        $entity = new MasterBrand('masterbrand', 'masterbrand' , 'masterbrand');
        $createdAt = new DateTime("U");
        $modifiedAt = new DateTime("U");

        new RefOptions(
            'guid',
            'projectid',
            $entity,
            'admin',
            $createdAt,
            $modifiedAt
        );

    }
}
