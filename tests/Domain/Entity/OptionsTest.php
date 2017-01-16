<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

class OptionsTest extends PHPUnit_Framework_TestCase
{
    public function testConstructor()
    {
        $options = new Options([
            'one' => $one = 1,
            'two' => $two = 'two2',
        ]);

        $this->assertSame($one, $options->getOption('one'));
        $this->assertSame($two, $options->getOption('two'));
        $this->assertNull($options->getOption('notreal'));
    }
}
