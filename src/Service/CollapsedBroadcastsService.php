<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;

class CollapsedBroadcastsService extends AbstractService
{
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

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
        int $month,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ) : array {

        $broadcasts = $this->repository->findByProgrammeAndMonth(
            $programme->getDbAncestryIds(),
            'Broadcast',
            $year,
            $month,
            $limit,
            $this->getOffset($limit, $page)
        );

        $services = $this->fetchUsedServices($broadcasts);
        return $this->setBroadcastsServices($broadcasts, $services);
    }

    public function findPastCollapsedBroadcastsForProgramme(
        Programme $programme,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findPastCollapsedBroadcastsForProgramme(
            $programme->getDbAncestryIds(),
            'Broadcast',
            $limit,
            $this->getOffset($limit, $page)
        );

        $services = $this->fetchUsedServices($broadcasts);
        return $this->setBroadcastsServices($broadcasts, $services);
    }

    private function fetchUsedServices(array $broadcasts): array
    {
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
        return array_reduce(
            $this->serviceRepository->findBySids($serviceIds),
            function ($memo, $service) {
                $memo[$service['sid']] = $service;
                return $memo;
            },
            []
        );
    }

    private function setBroadcastsServices(array $broadcasts, array $services): array
    {
        // Map all the entities. As we need to pass a parameter to the mapper, we need to use mapSingleEntity
        // instead of using mapManyEntities
        return array_map(function ($broadcast) use ($services) {
            return $this->mapSingleEntity($broadcast, $services);
        }, $broadcasts);
    }
}
