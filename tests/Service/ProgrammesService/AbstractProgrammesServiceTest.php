<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\ProgrammesService;
use PHPUnit_Framework_TestCase;

abstract class AbstractProgrammesServiceTest extends PHPUnit_Framework_TestCase
{
    protected $mockRepository;

    protected $mockMapper;

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

    protected function programmesService()
    {
        return new ProgrammesService($this->mockRepository, $this->mockMapper);
    }
}
