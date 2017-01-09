<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
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

    /**
     * @param array $dbMasterBrand
     * @return MasterBrand|null
     */
    public function getDomainModel(array $dbMasterBrand)
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

    private function getImageModel($dbMasterBrand, $key = 'image')
    {
        if (!isset($dbMasterBrand[$key])) {
            // Use default Image
            return $this->mapperFactory->getImageMapper()->getDefaultImage();
        }

        return $this->mapperFactory->getImageMapper()->getDomainModel($dbMasterBrand[$key]);
    }

    private function getNetworkModel($dbMasterBrand, $key = 'network')
    {
        if (!isset($dbMasterBrand[$key])) {
            return null;
        }

        return $this->mapperFactory->getNetworkMapper()->getDomainModel($dbMasterBrand[$key]);
    }

    private function getCompetitionWarningModel($dbMasterBrand, $key = 'competitionWarning')
    {
        if (!isset($dbMasterBrand[$key])) {
            return null;
        }

        return $this->mapperFactory->getVersionMapper()->getDomainModel($dbMasterBrand[$key]);
    }
}
