<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedService;
use BBC\ProgrammesPagesService\Domain\ValueObject\Nid;

class NetworkMapper extends AbstractMapper
{
    public function getDomainModel(array $dbNetwork): Network
    {
        return new Network(
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

    private function getImageModel($dbMasterBrand, $key = 'image')
    {
        if (!array_key_exists($key, $dbMasterBrand) || is_null($dbMasterBrand[$key])) {
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
