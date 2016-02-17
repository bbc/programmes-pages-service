<?php

namespace Tests\BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\EntityCollectionServiceResult;
use BBC\ProgrammesPagesService\Service\EntitySingleServiceResult;
use BBC\ProgrammesPagesService\Service\ProgrammesService;
use PHPUnit_Framework_TestCase;

class ProgrammesServiceTest extends PHPUnit_Framework_TestCase
{
    private $mockRepository;

    private $mockMapper;

    public function setUp()
    {
        $this->mockRepository = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CoreEntityRepository'
        );

        $this->mockMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper'
        );

        // A mapper that shall return a mock entity
        $this->mockMapper->method('getDomainModel')
            ->will($this->returnCallback([$this, 'programmeFromDbData']));
    }

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

    public function testFindByPid()
    {
        $pid = new Pid('b010t19z');
        $dbData = ['pid' => 'b010t19z'];

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn($dbData);

        $expectedResult = new EntitySingleServiceResult($this->programmeFromDbData($dbData));

        $result = $this->programmesService()->findByPidFull($pid);
        $this->assertEquals($expectedResult, $result);
    }

    public function testFindByPidEmptyData()
    {
        $pid = new Pid('b010t19z');

        $this->mockRepository->expects($this->once())
            ->method('findByPidFull')
            ->with($this->equalTo($pid))
            ->willReturn(null);

        $expectedResult = new EntitySingleServiceResult(null);

        $result = $this->programmesService()->findByPidFull($pid);
        $this->assertEquals($expectedResult, $result);
    }

    public function programmesFromDbData(array $entities)
    {
        return array_map([$this, 'programmeFromDbData'], $entities);
    }

    public function programmeFromDbData(array $entity)
    {
        $mockProgramme = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Domain\Entity\Programme'
        );

        $mockProgramme->method('getPid')->willReturn($entity['pid']);
        return $mockProgramme;
    }

    private function programmesService()
    {
        return new ProgrammesService($this->mockRepository, $this->mockMapper);
    }
}
