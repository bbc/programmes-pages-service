<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Service\EntityCollectionServiceResult;

class ProgrammesServiceFindAllTest extends AbstractProgrammesServiceTest
{
    public function testFindAllDefaultPagination()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $this->mockRepository->expects($this->once())
            ->method('findAllWithParents')
            ->with($this->equalTo(50), $this->equalTo(0))
            ->willReturn($dbData);

        $expectedResult = new EntityCollectionServiceResult(
            $this->programmesFromDbData($dbData),
            50,
            1
        );

        $result = $this->programmesService()->findAll();
        $this->assertEquals($expectedResult, $result);
    }

    public function testFindAllCustomPagination()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $this->mockRepository->expects($this->once())
            ->method('findAllWithParents')
            ->with($this->equalTo(5), $this->equalTo(10))
            ->willReturn($dbData);

        $expectedResult = new EntityCollectionServiceResult(
            $this->programmesFromDbData($dbData),
            5,
            3
        );

        $result = $this->programmesService()->findAll(5, 3);
        $this->assertEquals($expectedResult, $result);
    }

    public function testCountAll()
    {
        $this->mockRepository->expects($this->once())
            ->method('countAll')
            ->willReturn(10);

        $this->assertEquals(10, $this->programmesService()->countAll());
    }
}
