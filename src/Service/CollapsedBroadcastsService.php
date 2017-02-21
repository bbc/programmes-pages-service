<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Category;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use DateTimeImmutable;

class CollapsedBroadcastsService extends AbstractService
{
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    public function __construct(
        CollapsedBroadcastRepository $repository,
        MapperInterface $mapper,
        ServiceRepository $serviceRepository
    ) {
        parent::__construct($repository, $mapper);
        $this->serviceRepository = $serviceRepository;
    }

    public function findByProgrammeAndMonth(
        Programme $programme,
        int $year,
        int $month,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findByProgrammeAndMonth(
            $programme->getDbAncestryIds(),
            false,
            $year,
            $month,
            $limit,
            $this->getOffset($limit, $page)
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts);

        return $this->mapManyEntities($broadcasts, $services);
    }

    public function findPastByProgramme(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findPastByProgramme(
            $programme->getDbAncestryIds(),
            false,
            ApplicationTime::getTime(),
            $limit,
            $this->getOffset($limit, $page)
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts);

        return $this->mapManyEntities($broadcasts, $services);
    }

    public function findUpcomingByProgramme(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findUpcomingByProgramme(
            $programme->getDbAncestryIds(),
            false,
            ApplicationTime::getTime(),
            $limit,
            $this->getOffset($limit, $page)
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts);

        return $this->mapManyEntities($broadcasts, $services);
    }

    public function countUpcomingByProgramme(Programme $programme): int
    {
        return $this->repository->countUpcomingByProgramme(
            $programme->getDbAncestryIds(),
            false,
            ApplicationTime::getTime()
        );
    }

    public function findByCategoryAndStartAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findByCategoryAncestryAndStartAtDateRange(
            $category->getDbAncestryIds(),
            false,
            $startDate,
            $endDate,
            $limit,
            $this->getOffset($limit, $page)
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts);

        return $this->mapManyEntities($broadcasts, $services);
    }

    public function findByCategoryAndEndAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findByCategoryAncestryAndEndAtDateRange(
            $category->getDbAncestryIds(),
            false,
            $startDate,
            $endDate,
            $limit,
            $this->getOffset($limit, $page)
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts);

        return $this->mapManyEntities($broadcasts, $services);
    }

    public function countByCategoryAndEndAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): int {
        return $this->repository->countByCategoryAncestryAndEndAtDateRange(
            $category->getDbAncestryIds(),
            false,
            $startDate,
            $endDate
        );
    }

    private function stripWebcasts(array $broadcasts): array
    {
        return array_reduce($broadcasts, function ($memo, $broadcast) {
            // loop through areWebcasts
            // if it is true then remove the corresponding value from serviceIDs and any other listings
            // if serviceIds is not empty then add this broadcast to the memo
            for ($i = 0; $i < count($broadcast['areWebcasts']); $i++) {
                if (!$broadcast['areWebcasts'][$i] && $broadcast['serviceIds'][$i] != CollapsedBroadcastRepository::NO_SERVICE) {
                    $memo[] = $broadcast;
                }
            }
            return $memo;
        }, []);
    }

    private function fetchUsedServices(array $broadcasts): array
    {
        // Build list of all serviceIds used across all broadcasts
        $serviceIds = array_keys(
            array_reduce(
                $broadcasts,
                function ($memo, $broadcast) {
                    foreach ($broadcast['serviceIds'] as $id) {
                        // do not memo absent services
                        if ($id != CollapsedBroadcastRepository::NO_SERVICE) {
                            $memo[$id] = true;
                        }
                    }

                    return $memo;
                },
                []
            )
        );

        // If there are no serviceIds to fetch, skip requesting them
        $services = [];
        if ($serviceIds) {
            $services = $this->serviceRepository->findByIds($serviceIds);
        }

        // Fetch all the used services, keyed by their id
        return array_reduce($services, function ($memo, $service) {
            $memo[$service['id']] = $service;
            return $memo;
        }, []);
    }
}
