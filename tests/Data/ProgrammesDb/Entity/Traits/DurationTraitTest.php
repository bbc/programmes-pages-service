<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\DurationTrait;
use PHPUnit_Framework_TestCase;

class DurationTraitTest extends PHPUnit_Framework_TestCase
{

    public function testCalculatingDuration()
    {
        /** @var DurationTrait $entity */
        $entity = $this->getMockForTrait('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Traits\DurationTrait');

        $entity->setStart(new \DateTime('2016-01-01 00:00:00'));
        $entity->setEnd(new \DateTime('2016-01-01 00:00:00'));
        $this->assertEquals(0, $entity->getDuration());
    }
}
