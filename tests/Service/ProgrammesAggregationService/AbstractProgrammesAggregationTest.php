<?php

namespace Tests\BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;

use BBC\ProgrammesPagesService\Service\ProgrammesAggregationService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractProgrammesAggregationTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CoreEntityRepository');
        $this->setUpMapper('CoreEntityMapper', 'programmeFromDbData');
    }

    protected function programmeFromDbData($entity)
    {
        return $this->createMock(self::ENTITY_NS . ucfirst($entity['type']));
    }

    protected function service()
    {
        return new ProgrammesAggregationService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    protected function setUpMapper($mapperName, $entityBuilderMethod)
    {
        $this->mockMapper = $this->createMock($this::MAPPER_NS . $mapperName);
        $this->mockMapper->expects($this->any())
             ->method('getDomainModel')
             ->will($this->returnCallback(function ($unmappedDbEntity) use ($entityBuilderMethod) {
                 return call_user_func([$this, $entityBuilderMethod], $unmappedDbEntity);
             }));
    }
}
