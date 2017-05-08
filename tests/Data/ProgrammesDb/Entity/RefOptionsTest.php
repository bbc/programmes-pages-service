<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefMediaSet;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\RefOptions;
use PHPUnit_Framework_TestCase;
use ReflectionClass;

class RefOptionsTest extends PHPUnit_Framework_TestCase
{
    public function testTraits()
    {
        $reflection = new ReflectionClass(RefOptions::CLASS);
        $this->assertEquals([
            'Gedmo\Timestampable\Traits\TimestampableEntity',
            ], $reflection->getTraitNames());
    }

    public function testDefaults()
    {
        $originalId = 'bbc_one';
        $coreEntity = $this->mockCoreEntity();
        $network = $this->mockNetwork();

        $options = new RefOptions($originalId, $coreEntity);
        $this->assertSame($originalId, $options->getOriginalId());
        $this->assertNull($options->getOptionsForNetwork());
        $this->assertSame($coreEntity, $options->getOptionsForCoreEntity());
        $this->assertSame($coreEntity, $options->getOptionsFor());


        $options = new RefOptions($originalId, $network);
        $this->assertSame($originalId, $options->getOriginalId());
        $this->assertNull($options->getOptionsForCoreEntity());
        $this->assertSame($network, $options->getOptionsForNetwork());
        $this->assertSame($network, $options->getOptionsFor());

        $this->assertNull($options->getId());
        $this->assertNull($options->getAdminOptions());
        $this->assertNull($options->getLocalOptions());
        $this->assertNull($options->getProjectSpace());
    }

    public function testSetters()
    {
        $options = new RefOptions('bbc_one', $this->mockNetwork());

        $options->setOriginalId($eastenders = 'b006m86d');
        $options->setAdminOptions($admin = ['adminoptions']);
        $options->setLocalOptions($local = ['localoptions']);
        $options->setProjectSpace($project = 'project');

        $this->assertSame($eastenders, $options->getOriginalId());
        $this->assertSame($admin, $options->getAdminOptions());
        $this->assertSame($local, $options->getLocalOptions());
        $this->assertSame($project, $options->getProjectSpace());
    }

    /**
     * @expectedException \InvalidArgumentException
     */
    public function testInvalidOptionsForThrowsExceptionOnConstruct()
    {
        new RefOptions(
            'id',
            'wrongwrongwrong'
        );
    }

    private function mockCoreEntity()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity');
    }

    private function mockNetwork()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\Network');
    }
}
