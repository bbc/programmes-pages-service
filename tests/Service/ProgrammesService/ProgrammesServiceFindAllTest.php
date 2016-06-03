<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

class ProgrammesServiceFindAllTest extends AbstractProgrammesServiceTest
{
    public function testFindAllDefaultPagination()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $this->mockRepository->expects($this->once())
            ->method('findAllWithParents')
            ->with(300, 0)
            ->willReturn($dbData);

        $result = $this->service()->findAll();
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testFindAllCustomPagination()
    {
        $dbData = [['pid' => 'b010t19z'], ['pid' => 'b00swyx1']];

        $this->mockRepository->expects($this->once())
            ->method('findAllWithParents')
            ->with(5, 10)
            ->willReturn($dbData);

        $result = $this->service()->findAll(5, 3);
        $this->assertEquals($this->programmesFromDbData($dbData), $result);
    }

    public function testCountAll()
    {
        $this->mockRepository->expects($this->once())
            ->method('countAll')
            ->willReturn(10);

        $this->assertEquals(10, $this->service()->countAll());
    }
}
