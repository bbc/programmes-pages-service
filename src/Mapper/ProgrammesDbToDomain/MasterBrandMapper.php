<?php

namespace BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain;

use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Domain\ValueObject\Mid;

class MasterBrandMapper extends AbstractMapper
{
    public function getDomainModel(array $dbMasterBrand): MasterBrand
    {
        return new MasterBrand(
            new Mid($dbMasterBrand['mid']),
            $dbMasterBrand['name'],
            $this->getImageModel($dbMasterBrand),
            $this->getNetworkModel($dbMasterBrand),
            $this->getCompetitionWarningModel($dbMasterBrand)
        );
    }

    private function getImageModel($dbMasterBrand, $key = 'image')
    {
        if (!array_key_exists($key, $dbMasterBrand) || is_null($dbMasterBrand[$key])) {
            // Use default Image
            return $this->mapperProvider->getImageMapper()->getDefaultImage();
        }

        return $this->mapperProvider->getImageMapper()->getDomainModel($dbMasterBrand[$key]);
    }

    private function getNetworkModel($dbMasterBrand, $key = 'network')
    {
        if (!array_key_exists($key, $dbMasterBrand) || is_null($dbMasterBrand[$key])) {
            return null;
        }

        return $this->mapperProvider->getNetworkMapper()->getDomainModel($dbMasterBrand[$key]);
    }

    private function getCompetitionWarningModel($dbMasterBrand, $key = 'competitionWarning')
    {
        if (!array_key_exists($key, $dbMasterBrand) || is_null($dbMasterBrand[$key])) {
            return null;
        }

        return $this->mapperProvider->getVersionMapper()->getDomainModel($dbMasterBrand[$key]);
    }
}
