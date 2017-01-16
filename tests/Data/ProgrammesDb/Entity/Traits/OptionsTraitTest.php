<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\OptionsTrait;
use PHPUnit_Framework_TestCase;

class OptionsTraitTest extends PHPUnit_Framework_TestCase
{
    public function testDefaults()
    {
        /** @var OptionsTrait $entity */
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\OptionsTrait');

        $this->assertNull($entity->getOptions());
    }

    public function testSetters()
    {
        /** @var OptionsTrait $entity */
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\OptionsTrait');

        $entity->setOptions($options = [
            'one' => 1,
        ]);
        $this->assertEquals($options, $entity->getOptions());

        $entity->setOptions(null);
        $this->assertNull($entity->getOptions());
    }
}
