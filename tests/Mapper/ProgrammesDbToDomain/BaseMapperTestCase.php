<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use PHPUnit\Framework\TestCase;

abstract class BaseMapperTestCase extends TestCase
{
    protected function getMapperFactory(array $config = [])
    {
        $mockMapperFactory = $this->createMock('BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MapperFactory');

        foreach ($config as $name => $mock) {
            $mockMapperFactory->expects($this->any())
                ->method('get' . $name)
                ->willReturn($mock);
        }

        return $mockMapperFactory;
    }
}
