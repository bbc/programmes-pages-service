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
        $this->mockRepository = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\\' . $repositoryName
        );
    }

    protected function setUpMapper($mapperName, $entityBuilderMethod)
    {
        $this->mockMapper = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\\' . $mapperName
        );

        // A mapper that shall return a mock entity
        $this->mockMapper->method('getDomainModel')
            ->will($this->returnCallback(function ($entity) use ($entityBuilderMethod) {
                return call_user_func([$this, $entityBuilderMethod], $entity);
            }));
    }

    protected function mockEntity($name)
    {
        return $this->getMockWithoutInvokingTheOriginalConstructor(
            self::ENTITY_NS . $name
        );
    }
}
