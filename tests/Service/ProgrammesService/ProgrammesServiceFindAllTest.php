<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

class ProgrammesServiceFindAllTest extends AbstractProgrammesServiceTest
{
    public function testFindAllDefaultPagination()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $this->mockRepository->expects($this->once())
            ->method('findAllWithParents')
            ->with($this->equalTo(50), $this->equalTo(0))
            ->willReturn($dbData);

        $result = $this->programmesService()->findAll();
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testFindAllCustomPagination()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $this->mockRepository->expects($this->once())
            ->method('findAllWithParents')
            ->with($this->equalTo(5), $this->equalTo(10))
            ->willReturn($dbData);

        $result = $this->programmesService()->findAll(5, 3);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testCountAll()
    {
        $this->mockRepository->expects($this->once())
            ->method('countAll')
            ->willReturn(10);

        $this->assertEquals(10, $this->programmesService()->countAll());
    }
}
