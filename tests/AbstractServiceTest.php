<?php

namespace Tests\BBC\ProgrammesPagesService;

use PHPUnit_Framework_TestCase;

abstract class AbstractServiceTest extends PHPUnit_Framework_TestCase
{
    const ENTITY_NS = 'BBC\ProgrammesPagesService\Domain\Entity\\';

    protected $mockRepository;

    protected $mockMapper;

    protected function setUpRepo($repositoryName)
    {
        $this->mockRepository = $this->createMock('BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\\' . $repositoryName);
    }

    protected function setUpMapper($mapperName, $entityBuilderMethod)
    {
        $this->mockMapper = $this->createMock('BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\' . $mapperName);

        $this->mockMapper->expects($this->any())
            ->method('getDomainModel')
            ->will($this->returnCallback(function ($entity) use ($entityBuilderMethod) {
                return call_user_func([$this, $entityBuilderMethod], $entity);
            }));
    }

    protected function mockEntity($name)
    {
        return $this->createMock(self::ENTITY_NS . $name);
    }
}
