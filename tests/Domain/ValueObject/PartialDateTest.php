<?php

namespace Tests\BBC\ProgrammesPagesService\Domain\ValueObject;

use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use DateTime;
use PHPUnit\Framework\TestCase;

class PartialDateTest extends TestCase
{
    /**
     * @dataProvider validDateDataProvider
     */
    public function testValidDates($input, $expectedOutput)
    {
        $pd = new PartialDate(...$input);

        $this->assertEquals($expectedOutput, (string) $pd);
    }

    public function validDateDataProvider()
    {
        return [
            // Everything present
            [[2015, 1, 2], '2015-01-02'],
            // Missing Day
            [[2015, 1 , 0], '2015-01-00'],
            [[2015, 1], '2015-01-00'],
            // Missing Month & Day
            [[2015], '2015-00-00'],
            [[2015, 0], '2015-00-00'],
            [[2015, 0, 0], '2015-00-00'],
        ];
    }

    public function testJsonSerialize()
    {
        $pd = new PartialDate(2015, 1, 2);
        $this->assertEquals('["2015-01-02"]', json_encode([$pd]));
    }

    public function testFormatMysql()
    {
        $pd = new PartialDate(2015, 1, 2);
        $this->assertEquals('2015-01-02', $pd->formatMysql());
    }

    /**
     * @dataProvider toDateTimeProvider
     */
    public function testToDateTime($input, $expectedDateTime)
    {
        $partialDate = new PartialDate(...$input);
        $this->assertEquals($expectedDateTime, $partialDate->asDateTime());
    }

    public function toDateTimeProvider()
    {
        return [
            [[2015, 1, 2], DateTime::createFromFormat('Y-m-d H:i:s e', '2015-01-02 00:00:00 UTC')],
            [[2014, 6, 6], DateTime::createFromFormat('Y-m-d H:i:s e', '2014-06-06 00:00:00 UTC')],
            // Missing Day
            [[2015, 1 , 0], DateTime::createFromFormat('Y-m-d H:i:s e', '2015-01-01 00:00:00 UTC')],
            [[2015, 1], DateTime::createFromFormat('Y-m-d H:i:s e', '2015-01-01 00:00:00 UTC')],
            // Missing Month & Day
            [[2015], DateTime::createFromFormat('Y-m-d H:i:s e', '2015-01-01 00:00:00 UTC')],
            [[2015, 0], DateTime::createFromFormat('Y-m-d H:i:s e', '2015-01-01 00:00:00 UTC')],
            [[2015, 0, 0], DateTime::createFromFormat('Y-m-d H:i:s e', '2015-01-01 00:00:00 UTC')],
        ];
    }

    /**
     * @dataProvider invalidDateDataProvider
     * @expectedException InvalidArgumentException
     * @expectedExceptionMessage Could not create a PartialDate from parameters
     */
    public function testInvalidDates($input)
    {
        $pd = new PartialDate(...$input);
    }

    public function invalidDateDataProvider()
    {
        return [
            // Badly formated
            [[20151202]],
            [[2015, 1202]],
            // Invalid Month
            [[2015, 13, 1]],
            // Invalid Day
            [[2015, 1, 40]],
            [[2015, 2, 29]],
        ];
    }
}
