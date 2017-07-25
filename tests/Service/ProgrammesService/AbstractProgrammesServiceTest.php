<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesService;

use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\ProgrammesService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractProgrammesServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CoreEntityRepository');
        $this->setUpMapper('CoreEntityMapper', 'programmeFromDbData');
    }

    protected function programmesFromDbData(array $entities)
    {
        return array_map([$this, 'programmeFromDbData'], $entities);
    }

    protected function programmeFromDbData(array $entity)
    {
        $mockProgramme = $this->createMock(self::ENTITY_NS . 'Programme');

        $mockProgramme->method('getPid')->willReturn(new Pid($entity['pid']));
        return $mockProgramme;
    }

    protected function service()
    {
        return new ProgrammesService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    protected function setUpMapper($mapperName, $entityBuilderMethod)
    {
        $this->mockMapper = $this->createMock($this::MAPPER_NS . $mapperName);

        $this->mockMapper->expects($this->any())
            ->method('getDomainModelForProgramme')
            ->will($this->returnCallback(function ($entity) use ($entityBuilderMethod) {
                return call_user_func([$this, $entityBuilderMethod], $entity);
            }));
    }
}
