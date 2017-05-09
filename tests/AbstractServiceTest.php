<?php

namespace Tests\BBC\ProgrammesPagesService;

use PHPUnit\Framework\TestCase;

abstract class AbstractServiceTest extends TestCase
{
    const ENTITY_NS = 'BBC\ProgrammesPagesService\Domain\Entity\\';
    const REPOSITORY_NS = 'BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\\';
    const MAPPER_NS = 'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\';

    protected $mockRepository;

    protected $mockMapper;

    protected function setUpRepo($repositoryName)
    {
        $this->mockRepository = $this->getRepo($repositoryName);
    }

    protected function getRepo($repositoryName)
    {
        return $this->createMock($this::REPOSITORY_NS . $repositoryName);
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

    protected function mockEntity($name, $dbId = null)
    {
        $entity = $this->createMock(self::ENTITY_NS . $name);

        if ($dbId) {
            $entity->method('getDbId')->willReturn($dbId);
        }

        return $entity;
    }
}
