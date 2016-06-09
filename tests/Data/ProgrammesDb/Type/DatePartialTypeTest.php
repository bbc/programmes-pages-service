<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Type;

use Doctrine\DBAL\Types\Type;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\Type\DatePartialType;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use PHPUnit_Framework_TestCase;

class DatePartialTypeTest extends PHPUnit_Framework_TestCase
{
    private $type;

    private $platform;

    public function setUp()
    {
        if (!Type::hasType('date_partial')) {
            Type::addType('date_partial', 'BBC\ProgrammesPagesService\Data\ProgrammesDb\Type\DatePartialType');
        }

        $this->type = Type::getType('date_partial');
        $this->platform = $this->createMock('Doctrine\DBAL\Platforms\AbstractPlatform');
    }

    public function testBasics()
    {
        $this->assertEquals('date_partial', $this->type->getName());
        $this->assertEquals(true, $this->type->requiresSQLCommentHint($this->platform));
    }

    public function testToDbValueValid()
    {
        $this->assertEquals(null, $this->type->convertToDatabaseValue(null, $this->platform));

        $this->assertEquals('2015-01-00', $this->type->convertToDatabaseValue(new PartialDate(2015, 1), $this->platform));
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testToDbValueInvalid()
    {
        $this->type->convertToDatabaseValue(3, $this->platform);
    }

    public function testToPhpValueValid()
    {
        $this->assertEquals(null, $this->type->convertToPHPValue(null, $this->platform));

        $this->assertEquals(new PartialDate(2015, 1), $this->type->convertToPHPValue(new PartialDate(2015, 1), $this->platform));
        $this->assertEquals(new PartialDate(2015, 1), $this->type->convertToPHPValue('2015-01-00', $this->platform));
    }

    /**
     * @expectedException \Doctrine\DBAL\Types\ConversionException
     */
    public function testToPhpValueInvalid()
    {
        $this->type->convertToPHPValue("-3", $this->platform);
    }
}
