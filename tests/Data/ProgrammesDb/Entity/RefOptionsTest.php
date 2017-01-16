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
        $coreEntity = $this->mockCoreEntity();
        $masterBrand = $this->mockMasterBrand();

        $options = new RefOptions($coreEntity);
        $this->assertNull($options->getOptionsForNetwork());
        $this->assertSame($coreEntity, $options->getOptionsForCoreEntity());
        $this->assertSame($coreEntity, $options->getOptionsFor());


        $options = new RefOptions($masterBrand);
        $this->assertNull($options->getOptionsForCoreEntity());
        $this->assertSame($masterBrand, $options->getOptionsForNetwork());
        $this->assertSame($masterBrand, $options->getOptionsFor());

        $this->assertNull($options->getId());
        $this->assertNull($options->getAdminOptions());
        $this->assertNull($options->getLocalOptions());
        $this->assertNull($options->getProjectSpace());
    }

    public function testSetters()
    {
        $options = new RefOptions($this->mockMasterBrand());

        $options->setAdminOptions($admin = ['adminoptions']);
        $options->setLocalOptions($local = ['localoptions']);
        $options->setProjectSpace($project = 'project');

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
            'wrongwrongwrong'
        );
    }

    private function mockCoreEntity()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\CoreEntity');
    }

    private function mockMasterBrand()
    {
        return $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\Entity\MasterBrand');
    }
}
