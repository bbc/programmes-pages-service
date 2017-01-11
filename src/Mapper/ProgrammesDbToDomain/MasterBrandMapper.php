<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\Image;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\Entity\Network;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\Unfetched\UnfetchedVersion;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;

class MasterBrandMapper extends AbstractMapper
{
    private $cache = [];

    public function getCacheKey(array $dbMasterBrand): string
    {
        return $this->buildCacheKey($dbMasterBrand, 'id', [
            'image' => 'Image',
            'competitionWarning' => 'Version',
            'network' => 'Network',
        ]);
    }

    public function getDomainModel(array $dbMasterBrand): ?MasterBrand
    {
        $cacheKey = $this->getCacheKey($dbMasterBrand);

        if (!isset($this->cache[$cacheKey])) {
            // A MasterBrand must have a Network attached to it.
            // A MasterBrand without a Network is not valid.
            // It may temporarily occur in the database in the time between creating
            // a new MasterBrand and the Networks denorm running (which creates the
            // network entity for that MasterBrand), however we consider this
            // incomplete data and we should treat it as though the MasterBrand does
            // not exist, as it is in an incomplete state.
            $network = $this->getNetworkModel($dbMasterBrand);
            if (!$network) {
                $this->cache[$cacheKey] = null;
            } else {
                $this->cache[$cacheKey] = new MasterBrand(
                    new Mid($dbMasterBrand['mid']),
                    $dbMasterBrand['name'],
                    $this->getImageModel($dbMasterBrand),
                    $network,
                    $this->getCompetitionWarningModel($dbMasterBrand)
                );
            }
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

    private function getNetworkModel(array $dbMasterBrand, string $key = 'network'): ?Network
    {
        if (!isset($dbMasterBrand[$key])) {
            return null;
        }

        return $this->mapperFactory->getNetworkMapper()->getDomainModel($dbMasterBrand[$key]);
    }

    private function getCompetitionWarningModel(array $dbMasterBrand, string $key = 'competitionWarning'): ?Version
    {
        // It is possible to have no competition warning, where the key does
        // exist but is set to null. We'll only say it's Unfetched
        // if the key doesn't exist at all.
        if (!array_key_exists($key, $dbMasterBrand)) {
            return new UnfetchedVersion();
        }

        if (is_null($dbMasterBrand[$key])) {
            return null;
        }

        return $this->mapperFactory->getVersionMapper()->getDomainModel($dbMasterBrand[$key]);
    }
}
