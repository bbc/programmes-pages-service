<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Mapper\MapperInterface;

abstract class AbstractMapper implements MapperInterface
{
    protected $mapperProvider;

    public function __construct(MapperProvider $mapperProvider)
    {
        $this->mapperProvider = $mapperProvider;
    }
}
