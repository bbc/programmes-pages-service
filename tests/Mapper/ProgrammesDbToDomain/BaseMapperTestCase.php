<?php

namespace Tests\BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ProgrammeMapper;
use BBC\ProgrammesPagesService\Domain\Entity\Series;
use BBC\ProgrammesPagesService\Domain\Enumeration\IsPodcastableEnum;
use BBC\ProgrammesPagesService\Domain\ValueObject\PartialDate;
use BBC\ProgrammesPagesService\Domain\ValueObject\Pid;
use PHPUnit_Framework_TestCase;

abstract class BaseMapperTestCase extends PHPUnit_Framework_TestCase
{
    protected function getMapperProvider(array $config)
    {
        $mockMapperProvider = $this->getMockWithoutInvokingTheOriginalConstructor(
            'BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\mapperProvider'
        );

        foreach ($config as $name => $mock) {
            $mockMapperProvider->expects($this->any())
                ->method('get' . $name)
                ->willReturn($mock);
        }

        return $mockMapperProvider;
    }
}
