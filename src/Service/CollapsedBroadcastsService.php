<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\ServiceMapper;

class CollapsedBroadcastsService extends AbstractService
{
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    /**
     * @var ServiceMapper
     */
    protected $serviceMapper;

    public function __construct(
        BroadcastRepository $repository,
        MapperInterface $mapper,
        ServiceRepository $serviceRepository
    ) {
        parent::__construct($repository, $mapper);
        $this->serviceRepository = $serviceRepository;
    }

    public function findCollapsedBroadcastsByProgrammeAndMonth(
        Programme $programme,
        int $year,
        int $month
    ) : array {

        $broadcasts = $this->repository->findByProgrammeAndMonth(
            $programme->getDbAncestryIds(),
            'Broadcast',
            $year,
            $month
        );

        // Build list of all serviceIds used across all broadcasts
        $serviceIds = array_keys(
            array_reduce(
                $broadcasts,
                function ($memo, $broadcast) {
                    foreach ($broadcast['serviceIds'] as $sid) {
                        $memo[$sid] = true;
                    }

                    return $memo;
                },
                []
            )
        );

        // Fetch all the used services
        $services = array_reduce(
            $this->serviceRepository->findByIds($serviceIds),
            function ($memo, $service) {
                $memo[$service['sid']] = $service;
                return $memo;
            },
            []
        );

        // Map all the entities. As we need to pass a parameter to the mapper, we need to use mapSingleEntity
        // instead of using mapManyEntities
        $domainModels = [];
        foreach ($broadcasts as $broadcast) {
            $domainModels[] = $this->mapSingleEntity($broadcast, $services);
        }

        return $domainModels;
    }
}
