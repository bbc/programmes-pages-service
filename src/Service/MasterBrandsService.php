<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesCachingLibrary\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\MasterBrandRepository;
use BBC\ProgrammesPagesService\Domain\Entity\MasterBrand;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\MasterBrandMapper;

class MasterBrandsService extends AbstractService
{
    /* @var MasterBrandMapper */
    protected $mapper;
    /* @var MasterBrandRepository */
    protected $repository;

    public function __construct(
        MasterBrandRepository $repository,
        MasterBrandMapper $mapper,
        CacheInterface $cache
    ) {
        parent::__construct($repository, $mapper, $cache);
    }

    public function findByMid($mid): ?MasterBrand
    {
        $result = $this->repository->findByMid($mid);

        return $this->mapSingleEntity($result);
    }
}
