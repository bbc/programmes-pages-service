<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory;
use PHPUnit\Framework\TestCase;

abstract class BaseMapperTestCase extends TestCase
{
    protected function getMapperFactory(array $config = [], array $factoryOptions = [])
    {
        $mockedMethods = [];
        foreach ($config as $name => $mock) {
            $mockedMethods[] = 'get' . $name;
        }

        $mockMapperFactory = $this->getMockBuilder(MapperFactory::class)
            ->setConstructorArgs([$factoryOptions])
            ->setMethods($mockedMethods)
            ->getMock();

        foreach ($config as $name => $mock) {
            $mockMapperFactory->expects($this->any())
                ->method('get' . $name)
                ->willReturn($mock);
        }

        return $mockMapperFactory;
    }
}
