<?php

namespace Tests\BBC\ProgrammesPagesService\Service\CoreEntityService;

use BBC\ProgrammesPagesService\Domain\Entity\CoreEntity;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use BBC\ProgrammesPagesService\Service\CoreEntityService;
use Tests\BBC\ProgrammesPagesService\AbstractServiceTest;

abstract class AbstractCoreEntityServiceTest extends AbstractServiceTest
{
    public function setUp()
    {
        $this->setUpCache();
        $this->setUpRepo('CoreEntityRepository');
        $this->setUpMapper('CoreEntityMapper', 'coreEntityFromDbData');
    }

    protected function coreEntityFromDbData(array $entity)
    {
        $mockCoreEntity = $this->createMock(CoreEntity::class);

        $mockCoreEntity->method('getPid')->willReturn(new Pid($entity['pid']));
        return $mockCoreEntity;
    }

    protected function service()
    {
        return new CoreEntityService($this->mockRepository, $this->mockMapper, $this->mockCache);
    }

    protected function setUpMapper($mapperName, $entityBuilderMethod)
    {
        $this->mockMapper = $this->createMock($this::MAPPER_NS . $mapperName);

        $this->mockMapper->expects($this->any())
            ->method('getDomainModel')
            ->will($this->returnCallback(function ($entity) use ($entityBuilderMethod) {
                return call_user_func([$this, $entityBuilderMethod], $entity);
            }));
    }
}
