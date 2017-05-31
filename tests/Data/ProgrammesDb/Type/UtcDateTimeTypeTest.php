<?php

namespace Tests\BBC\ProgrammesPagesService\Data\ProgrammesDb\Type;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\Type\UtcDateTimeType;
use Cake\Chronos\Chronos;
use Doctrine\DBAL\Platforms\AbstractPlatform;
use Doctrine\DBAL\Types\Type;
use InvalidArgumentException;
use PHPUnit\Framework\TestCase;

class UtcDateTimeTypeTest extends TestCase
{
    /**
     * @var UtcDateTimeType
     */
    private $type;

    private $platform;

    public function setUp()
    {
        if (!Type::hasType('datetime')) {
            Type::addType('datetime', UtcDateTimeType::class);
        } elseif (Type::getType('datetime') != UtcDateTimeType::class) {
            Type::overrideType('datetime', UtcDateTimeType::class);
        }

        $this->type = Type::getType('datetime');
        $this->platform = $this->getMockForAbstractClass(AbstractPlatform::class);
    }

    public function testNullPassedToConvertToDatabaseValue()
    {
        $this->assertNull($this->type->convertToDatabaseValue(null, $this->platform));
    }

    public function testInvalidDateTypePassedToConvertToDatabaseValue()
    {
        $this->expectException(InvalidArgumentException::class);
        $this->type->convertToDatabaseValue('', $this->platform);
    }

    /**
     * @dataProvider bstValueProvider
     *
     * @param callable $callable
     */
    public function testBstToDbValueValid(callable $callable)
    {
        $convertedValue = $this->type->convertToDatabaseValue($callable(), $this->platform);
        $this->assertEquals('2017-06-12 08:08:08', $convertedValue);
    }

    public function bstValueProvider(): array
    {
        return [
            'notimezone' => [
                function () {
                    return new Chronos('2017-06-12 08:08:08');
                },
            ],
            'londontimezone' => [
                function () {
                    return new Chronos('2017-06-12 09:08:08', 'Europe/London');
                },
            ],
        ];
    }

    /**
     * @dataProvider utcValueProvider
     *
     * @param callable $callable
     */
    public function testUtcToDbValueValid(callable $callable)
    {
        $convertedValue = $this->type->convertToDatabaseValue($callable(), $this->platform);
        $this->assertEquals('2017-12-12 08:08:08', $convertedValue);
    }

    public function utcValueProvider(): array
    {
        return [
            'notimezone' => [
                function () {
                    return new Chronos('2017-12-12 08:08:08');
                },
            ],
            'londontimezone' => [
                function () {
                    return new Chronos('2017-12-12 08:08:08', 'Europe/London');
                },
            ],
        ];
    }

    public function testConvertToPHPValueWithNull()
    {
        $this->assertNull($this->type->convertToPHPValue(null, $this->platform));
    }

    public function testConvertToPHPValueBst()
    {
        $phpValue = $this->type->convertToPHPValue('2017-06-12 08:08:08', $this->platform);
        $this->assertInstanceOf(Chronos::class, $phpValue);
        $this->assertEquals('2017-06-12 09:08:08', $phpValue->setTimezone('Europe/London')->format('Y-m-d H:i:s'));
    }

    public function testConvertToPHPValueUtc()
    {
        $phpValue = $this->type->convertToPHPValue('2017-12-12 08:08:08', $this->platform);
        $this->assertInstanceOf(Chronos::class, $phpValue);
        $this->assertEquals('2017-12-12 08:08:08', $phpValue->setTimezone('Europe/London')->format('Y-m-d H:i:s'));
    }
}
