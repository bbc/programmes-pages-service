<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Options;
use BBC\ProgrammesPagesService\Domain\Entity\Service;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;

class NetworkMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbNetwork): string
    {
        return $this->buildCacheKey($dbNetwork, 'id', [
            'image' => 'Image',
            'defaultService' => 'Service',
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
                $this->getOptionsModel($dbNetwork),
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

    private function getImageModel(array $dbMasterBrand, string $key = 'image'): ?Image
    {
        if (!isset($dbMasterBrand[$key])) {
            // Use default Image
            return $this->mapperFactory->getImageMapper()->getDefaultImage();
        }

        return $this->mapperFactory->getImageMapper()->getDomainModel($dbMasterBrand[$key]);
    }

    private function getServiceModel(array $dbNetwork, string $key = 'defaultService'): ?Service
    {
        if (!array_key_exists($key, $dbNetwork)) {
            return new UnfetchedService();
        }

        if (is_null($dbNetwork[$key])) {
            return null;
        }

        return $this->mapperFactory->getServiceMapper()->getDomainModel($dbNetwork[$key]);
    }

    private function getOptionsModel(array $dbNetwork, string $key = 'options'): Options
    {
        // Networks have no parents so this is simple. They don't care nor process any other options
        // in programmes hierarchy. We don't build any tree of options.
        return $this->mapperFactory->getOptionsMapper()->getDomainModel(
            $dbNetwork[$key] ?? []
        );
    }
}
