<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;

class NetworkMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbNetwork): string
    {
        return $this->buildCacheKey($dbNetwork, 'id', [
            'image' => 'Image',
            'service' => 'Service',
        ]);
    }

    public function getDomainModel(array $dbNetwork): Network
    {
        $cacheKey = $this->getCacheKey($dbNetwork);

        if (!isset($this->cache[$cacheKey])) {
            $this->cache[$cacheKey] = new Network(
                new Nid($dbNetwork['nid']),
                $dbNetwork['name'],
                $this->getImageModel($dbNetwork),
                $dbNetwork['urlKey'],
                $dbNetwork['type'],
                $dbNetwork['medium'],
                $this->getServiceModel($dbNetwork),
                $dbNetwork['isPublicOutlet'],
                $dbNetwork['isChildrens'],
                $dbNetwork['isWorldServiceInternational'],
                $dbNetwork['isInternational'],
                $dbNetwork['isAllowedAdverts']
            );
        }

        return $this->cache[$cacheKey];
    }

    private function getImageModel($dbMasterBrand, $key = 'image')
    {
        if (!isset($dbMasterBrand[$key])) {
            // Use default Image
            return $this->mapperFactory->getImageMapper()->getDefaultImage();
        }

        return $this->mapperFactory->getImageMapper()->getDomainModel($dbMasterBrand[$key]);
    }

    private function getServiceModel($dbNetwork, $key = 'defaultService')
    {
        if (!array_key_exists($key, $dbNetwork)) {
            return new UnfetchedService();
        }

        if (is_null($dbNetwork[$key])) {
            return null;
        }

        return $this->mapperFactory->getServiceMapper()->getDomainModel($dbNetwork[$key]);
    }
}
