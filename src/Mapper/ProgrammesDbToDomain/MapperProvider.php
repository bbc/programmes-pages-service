<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

class MapperProvider
{
    protected $mapperInstances = [];

    public function getImageMapper(): ImageMapper
    {
        if (!array_key_exists('ImageMapper', $this->mapperInstances)) {
            $this->mapperInstances['ImageMapper'] = new ImageMapper();
        }

        return $this->mapperInstances['ImageMapper'];
    }


    public function getMasterBrandMapper(): MasterBrandMapper
    {
        if (!array_key_exists('MasterBrandMapper', $this->mapperInstances)) {
            $this->mapperInstances['MasterBrandMapper'] = new MasterBrandMapper($this);
        }

        return $this->mapperInstances['MasterBrandMapper'];
    }

    public function getNetworkMapper(): NetworkMapper
    {
        if (!array_key_exists('NetworkMapper', $this->mapperInstances)) {
            $this->mapperInstances['NetworkMapper'] = new NetworkMapper($this);
        }

        return $this->mapperInstances['NetworkMapper'];
    }

    public function getProgrammeMapper(): ProgrammeMapper
    {
        if (!array_key_exists('ProgrammeMapper', $this->mapperInstances)) {
            $this->mapperInstances['ProgrammeMapper'] = new ProgrammeMapper($this);
        }

        return $this->mapperInstances['ProgrammeMapper'];
    }

    public function getServiceMapper(): ServiceMapper
    {
        if (!array_key_exists('ServiceMapper', $this->mapperInstances)) {
            $this->mapperInstances['ServiceMapper'] = new ServiceMapper($this);
        }

        return $this->mapperInstances['ServiceMapper'];
    }

    public function getVersionMapper(): VersionMapper
    {
        if (!array_key_exists('VersionMapper', $this->mapperInstances)) {
            $this->mapperInstances['VersionMapper'] = new VersionMapper($this);
        }

        return $this->mapperInstances['VersionMapper'];
    }
}
