<?php

namespace Tests\BBC\ProgrammesPagesService\Service\BroadcastsService;

class FindAllYearsAndMonthsByProgrammeTest extends AbstractBroadcastsServiceTest
{
    public function testFindAllYearsAndMonthsByProgramme()
    {
        $dbAncestry = [1, 2, 3];
        $programme = $this->mockEntity('Programme', 3);
        $programme->method('getDbAncestryIds')->willReturn($dbAncestry);

        $dbData = [
            ['year' => '2016', 'month' => '8'],
            ['year' => '2016', 'month' => '6'],
            ['year' => '2015', 'month' => '12'],
            ['year' => '2015', 'month' => '11'],
            ['year' => '2015', 'month' => '6'],
            ['year' => '2015', 'month' => '5'],
            ['year' => '2014', 'month' => '6'],
        ];

        $expectedResult = [
            2016 => [8, 6],
            2015 => [12, 11, 6, 5],
            2014 => [6],
        ];

        $this->mockRepository->expects($this->once())
            ->method('FindAllYearsAndMonthsByProgramme')
            ->with($dbAncestry)
            ->willReturn($dbData);

        $result = $this->service()->findAllYearsAndMonthsByProgramme($programme);
        $this->assertSame($expectedResult, $result);
    }
}
