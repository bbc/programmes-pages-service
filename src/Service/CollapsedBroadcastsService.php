<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
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
        BroadcastRepository $repository,
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
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findByProgrammeAndMonth(
            $programme->getDbAncestryIds(),
            'Broadcast',
            $year,
            $month,
            $limit,
            $this->getOffset($limit, $page)
        );

        $services = $this->fetchUsedServices($broadcasts);
        return $this->mapManyEntities($broadcasts, $services);
    }

    public function findPastByProgramme(
        Programme $programme,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findPastByProgramme(
            $programme->getDbAncestryIds(),
            'Broadcast',
            ApplicationTime::getTime(),
            $limit,
            $this->getOffset($limit, $page)
        );

        $services = $this->fetchUsedServices($broadcasts);
        return $this->mapManyEntities($broadcasts, $services);
    }

    public function findUpcomingByProgramme(
        Programme $programme,
        $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $broadcasts = $this->repository->findUpcomingByProgramme(
            $programme->getDbAncestryIds(),
            'Broadcast',
            ApplicationTime::getTime(),
            $limit,
            $this->getOffset($limit, $page)
        );

        $services = $this->fetchUsedServices($broadcasts);
        return $this->mapManyEntities($broadcasts, $services);
    }

    public function countUpcomingByProgramme(Programme $programme): int
    {
        return $this->repository->countUpcomingByProgramme(
            $programme->getDbAncestryIds(),
            'Broadcast',
            ApplicationTime::getTime()
        );
    }

    public function findByCategoryAndStartAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $medium = null,
        $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_PAGE
    ) {
        $broadcasts = $this->repository->findByCategoryAncestryAndStartAtDateRange(
            $category->getDbAncestryIds(),
            'Broadcast',
            $medium,
            $startDate,
            $endDate,
            $limit,
            $offset
        );

        $services = $this->fetchUsedServices($broadcasts);
        return $this->mapManyEntities($broadcasts, $services);
    }

    public function findByCategoryAndEndAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $medium = null,
        $limit = self::DEFAULT_LIMIT,
        int $offset = self::DEFAULT_OFFSET
    ) {
        $broadcasts = $this->repository->findByCategoryAncestryAndEndAtDateRange(
            $category->getDbAncestryIds(),
            'Broadcast',
            $medium,
            $startDate,
            $endDate,
            $limit,
            $offset
        );

        $services = $this->fetchUsedServices($broadcasts);
        return $this->mapManyEntities($broadcasts, $services);
    }

    public function countByCategoryAndEndAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        string $medium = null
    ) {
        return $this->repository->countByCategoryAncestryAndEndAtDateRange(
            $category->getDbAncestryIds(),
            'Broadcast',
            $medium,
            $startDate,
            $endDate
        );
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

        // If there are no serviceIds to fetch, skip requesting them
        $services = [];
        if ($serviceIds) {
            $services = $this->serviceRepository->findBySids($serviceIds);
        }

        // Fetch all the used services, keyed by their sid
        return array_reduce($services, function ($memo, $service) {
            $memo[$service['sid']] = $service;
            return $memo;
        }, []);
    }
}
