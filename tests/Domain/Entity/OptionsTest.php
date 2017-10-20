<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\Entity;

use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Map\ContactMediaMap;
use PHPUnit\Framework\TestCase;

class OptionsTest extends TestCase
{
    public function testConstructor()
    {
        $options = new Options([
            'one' => 1,
            'two' => 'two2',
        ]);

        $this->assertSame(1, $options->getOption('one'));
        $this->assertSame('two2', $options->getOption('two'));
        $this->assertNull($options->getOption('notreal'));
    }

    public function testOptionsBuildModelForContactsMedia()
    {
        $options = new Options([
           'one' => 1,
           'two' => 'two2',
           'contact_details' => new ContactMediaMap(),
        ]);

        $this->assertInstanceOf(ContactMediaMap::class, $options->getContactMediaMap());
    }
}
