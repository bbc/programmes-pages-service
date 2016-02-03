<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use InvalidArgumentException;

class MapperProvider
{
    protected $mapperInstances = [];

    public function getProgrammeMapper(): ProgrammeMapper
    {
        if (!array_key_exists('ProgrammeMapper', $this->mapperInstances)) {
            $this->mapperInstances['ProgrammeMapper'] = new ProgrammeMapper(
                $this->getImageMapper()
            );
        }

        return $this->mapperInstances['ProgrammeMapper'];
    }

    public function getImageMapper(): ImageMapper
    {
        if (!array_key_exists('ImageMapper', $this->mapperInstances)) {
            $this->mapperInstances['ImageMapper'] = new ImageMapper();
        }

        return $this->mapperInstances['ImageMapper'];
    }
}
