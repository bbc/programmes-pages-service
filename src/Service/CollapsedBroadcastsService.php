<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Category;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use DateTimeImmutable;
use BBC\ProgrammesPagesService\Cache\CacheInterface;

class CollapsedBroadcastsService extends AbstractService
{
    /**
     * @var ServiceRepository
     */
    protected $serviceRepository;

    public function __construct(
        CollapsedBroadcastRepository $repository,
        MapperInterface $mapper,
        CacheInterface $cache,
        ServiceRepository $serviceRepository
    ) {
        parent::__construct($repository, $mapper, $cache);
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

    public function findBroadcastYearsAndMonthsByProgramme(Programme $programme): array
    {
        $dbYearsAndMonths = $this->repository->findAllYearsAndMonthsByProgramme(
            $programme->getDbAncestryIds(),
            false
        );

        $result = [];

        foreach ($dbYearsAndMonths as $period) {
            $year = (int) $period['year'];
            if (!isset($result[$year])) {
                $result[$year] = [];
            }

            $result[$year][] = (int) $period['month'];
        }

        return $result;
    }

    public function findDaysByCategoryInDateRange(
        Category $category,
        DateTimeImmutable $start,
        DateTimeImmutable $end
    ): array {
        $rows = $this->repository->findBroadcastedDatesForCategory(
            $category->getDbAncestryIds(),
            false,
            $start,
            $end
        );

        $result = [];

        foreach ($rows as $row) {
            $year = (int) $row['year'];
            if (!isset($result[$year])) {
                $result[$year] = [];
            }

            $month = (int) $row['month'];
            if (!isset($result[$year][$month])) {
                $result[$year][$month] = [];
            }

            $day = (int) $row['day'];
            if (!in_array($day, $result[$year][$month])) {
                $result[$year][$month][] = $day;
            }
        }

        return $result;
    }

    public function filterCategoriesByBroadcastedDate(
        array $allCategories,
        DateTimeImmutable $from,
        DateTimeImmutable $to
    ): array {
        if (empty($allCategories)) {
            return [];
        }

        $categoriesAncestryIds = [];
        foreach ($allCategories as $category) {
            $categoriesAncestryIds[] = $category->getDbAncestryIds();
        }

        $broadcastedCategoriesAncestries = $this->repository->filterCategoriesByBroadcastedDates(
            $categoriesAncestryIds,
            false,
            $from,
            $to
        );

        $broadcastedAncestries = array_column($broadcastedCategoriesAncestries, 'ancestry');

        $broadcastedCategories = [];
        foreach ($allCategories as $category) {
            $ancestryCategory = implode(',', $category->getDbAncestryIds()) . ',';
            if (in_array($ancestryCategory, $broadcastedAncestries)) {
                $broadcastedCategories[] = $category;
            }
        }

        return $broadcastedCategories;
    }

    private function stripWebcasts(array $broadcasts): array
    {
        $withoutWebcasts = [];
        foreach ($broadcasts as $broadcast) {
            $cleanedBroadcast = $broadcast;
            $cleaned = false;
            foreach ($broadcast['areWebcasts'] as $i => $isWebcast) {
                if ($isWebcast || !$broadcast['serviceIds'][$i]) {
                    unset($cleanedBroadcast['areWebcasts'][$i]);
                    unset($cleanedBroadcast['serviceIds'][$i]);
                    unset($cleanedBroadcast['broadcastIds'][$i]);
                    $cleaned = true;
                }
            }
            if ($cleaned) {
                $cleanedBroadcast['areWebcasts'] = array_values($cleanedBroadcast['areWebcasts']);
                $cleanedBroadcast['serviceIds'] = array_values($cleanedBroadcast['serviceIds']);
                $cleanedBroadcast['broadcastIds'] = array_values($cleanedBroadcast['broadcastIds']);
            }
            $withoutWebcasts[] = $cleanedBroadcast;
        }
        return $withoutWebcasts;
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
