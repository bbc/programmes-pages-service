<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Cache\CacheInterface;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\CollapsedBroadcastRepository;
use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\ServiceRepository;
use BBC\ProgrammesPagesService\Domain\ApplicationTime;
use BBC\ProgrammesPagesService\Domain\Entity\Broadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Category;
use BBC\ProgrammesPagesService\Domain\Entity\CollapsedBroadcast;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\MapperInterface;
use DateTimeImmutable;

class CollapsedBroadcastsService extends AbstractService
{
    /* @var CollapsedBroadcastRepository */
    protected $repository;

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

    public function countUpcomingRepeatsAndDebutsByProgramme(Programme $programme, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $programme->getDbId(),
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
                return $this->repository->countUpcomingRepeatsAndDebutsByProgramme(
                    $programme->getDbAncestryIds(),
                    false,
                    ApplicationTime::getTime()
                );
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findByProgrammeAndMonth(
        Programme $programme,
        int $year,
        int $month,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $programme->getDbId(),
            $year,
            $month,
            $limit,
            $page,
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $year, $month, $limit, $page) {
                return $this->findByProgrammeAndMonthHelper($programme, $year, $month, $limit, $page, false);
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findByProgrammeAndMonthWithFullServicesOfNetworksList(
        Programme $programme,
        int $year,
        int $month,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $programme->getDbId(),
            $year,
            $month,
            $limit,
            $page,
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $year, $month, $limit, $page) {
                return $this->findByProgrammeAndMonthHelper($programme, $year, $month, $limit, $page, true);
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findNextDebutOrRepeatOnByProgramme(
        Programme $programme,
        $ttl = CacheInterface::NORMAL
    ): ?CollapsedBroadcast {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
                $result = $this->findNextDebutOrRepeatOnByProgrammeHelper($programme, false);
                return $result ? reset($result) : null;
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findNextDebutOrRepeatOnByProgrammeWithFullServicesOfNetworksList(
        Programme $programme,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::NORMAL
    ): ?CollapsedBroadcast {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
                $result = $this->findNextDebutOrRepeatOnByProgrammeHelper($programme, true);
                return $result ? reset($result) : null;
            },
            [],
            $nullTtl
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findPastByProgramme(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $limit, $page) {
                return $this->findPastByProgrammeHelper($programme, $limit, $page, false);
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findPastByProgrammeWithFullServicesOfNetworksList(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL,
        $nullTtl = CacheInterface::SHORT
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $limit, $page) {
                return $this->findPastByProgrammeHelper($programme, $limit, $page, true);
            },
            [],
            $nullTtl
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findUpcomingByProgramme(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $limit, $page) {
                return $this->findUpcomingByProgrammeHelper($programme, $limit, $page, false);
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findUpcomingByProgrammeWithFullServicesOfNetworksList(
        Programme $programme,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $limit, $page, $ttl);
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme, $limit, $page) {
                return $this->findUpcomingByProgrammeHelper($programme, $limit, $page, true);
            }
        );
    }

    public function findByBroadcast(
        Broadcast $broadcast,
        $ttl = CacheInterface::NORMAL
    ): ?CollapsedBroadcast {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $broadcast->getPid(),
            $broadcast->getProgrammeItem()->getPid(),
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($broadcast) {
                return $this->findByBroadcastHelper($broadcast, false);
            }
        );
    }

    public function findByBroadcastWithFullServicesOfNetworksList(
        Broadcast $broadcast,
        $ttl = CacheInterface::NORMAL
    ): ?CollapsedBroadcast {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $broadcast->getPid(),
            $broadcast->getProgrammeItem()->getPid(),
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($broadcast) {
                return $this->findByBroadcastHelper($broadcast, true);
            }
        );
    }

    public function countUpcomingByProgramme(Programme $programme, $ttl = CacheInterface::NORMAL): int
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
                return $this->repository->countUpcomingByProgramme(
                    $programme->getDbAncestryIds(),
                    false,
                    ApplicationTime::getTime()
                );
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findByCategoryAndStartAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $category->getDbId(),
            $startDate->getTimestamp(),
            $endDate->getTimestamp(),
            $limit,
            $page,
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $startDate, $endDate, $limit, $page) {
                return $this->findByCategoryAndStartAtDateRangeHelper($category, $startDate, $endDate, $limit, $page, false);
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findByCategoryAndStartAtDateRangeWithFullServicesOfNetworksList(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $category->getDbId(),
            $startDate->getTimestamp(),
            $endDate->getTimestamp(),
            $limit,
            $page,
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $startDate, $endDate, $limit, $page) {
                return $this->findByCategoryAndStartAtDateRangeHelper($category, $startDate, $endDate, $limit, $page, true);
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findByCategoryAndEndAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $category->getDbId(),
            $startDate->getTimestamp(),
            $endDate->getTimestamp(),
            $limit,
            $page,
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $startDate, $endDate, $limit, $page) {
                return $this->findByCategoryAndEndAtDateRangeHelper($category, $startDate, $endDate, $limit, $page, false);
            }
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    public function findByCategoryAndEndAtDateRangeWithFullServicesOfNetworksList(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(
            __CLASS__,
            __FUNCTION__,
            $category->getDbId(),
            $startDate->getTimestamp(),
            $endDate->getTimestamp(),
            $limit,
            $page,
            $ttl
        );
        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $startDate, $endDate, $limit, $page) {
                return $this->findByCategoryAndEndAtDateRangeHelper($category, $startDate, $endDate, $limit, $page, true);
            }
        );
    }

    public function countByCategoryAndEndAtDateRange(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        $ttl = CacheInterface::NORMAL
    ): int {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $category->getDbId(), $startDate->getTimestamp(), $endDate->getTimestamp(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $startDate, $endDate) {
                return $this->repository->countByCategoryAncestryAndEndAtDateRange(
                    $category->getDbAncestryIds(),
                    false,
                    $startDate,
                    $endDate
                );
            }
        );
    }

    /**
     * Return array looking like:
     *
     *   [
     *       2016 => [8, 6],
     *       2015 => [12, 11, 6, 5],
     *       2014 => [6],
     *   ]
     */
    public function findBroadcastYearsAndMonthsByProgramme(Programme $programme, $ttl = CacheInterface::NORMAL): array
    {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $programme->getDbId(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($programme) {
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
        );
    }

    /**
     * Return array looking like:
     *  [
     *       '2011' => [
     *           '8' => [1, 2],
     *       ],
     *       '2010' => [
     *           '2' => [7]
     *       ]
     *  ]
     */
    public function findDaysByCategoryInDateRange(
        Category $category,
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        $ttl = CacheInterface::NORMAL
    ): array {
        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, $category->getDbId(), $start->getTimestamp(), $end->getTimestamp(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($category, $start, $end) {
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
        );
    }

    public function filterCategoriesByBroadcastedDate(
        array $allCategories,
        DateTimeImmutable $from,
        DateTimeImmutable $to,
        $ttl = CacheInterface::NORMAL
    ): array {
        if (empty($allCategories)) {
            return [];
        }

        $categoriesIds = [];
        foreach ($allCategories as $category) {
            $categoriesIds[] = $category->getDbId();
        }

        $key = $this->cache->keyHelper(__CLASS__, __FUNCTION__, implode('|', $categoriesIds), $from->getTimestamp(), $to->getTimestamp(), $ttl);

        return $this->cache->getOrSet(
            $key,
            $ttl,
            function () use ($allCategories, $from, $to) {
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
        );
    }

    /**
     * @return CollapsedBroadcast[]
     */
    protected function findByProgrammeAndMonthHelper(
        Programme $programme,
        int $year,
        int $month,
        ?int $limit,
        int $page,
        bool $getFullListOfServicesForNetwork
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
        $services = $this->fetchUsedServices($broadcasts, $getFullListOfServicesForNetwork);

        return $this->mapManyEntities($broadcasts, $services);
    }

    /**
     * @return CollapsedBroadcast[]
     */
    protected function findNextDebutOrRepeatOnByProgrammeHelper(
        Programme $programme,
        bool $getFullListOfServicesForNetwork
    ): array {
        $broadcasts = $this->repository->findNextDebutOrRepeatOnByProgramme(
            $programme->getDbAncestryIds(),
            false,
            ApplicationTime::getTime()
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts, $getFullListOfServicesForNetwork);

        return $this->mapManyEntities($broadcasts, $services);
    }

    /**
     * @return CollapsedBroadcast[]
     */
    protected function findPastByProgrammeHelper(
        Programme $programme,
        ?int $limit,
        int $page,
        bool $getFullListOfServicesForNetwork
    ): array {
        $broadcasts = $this->repository->findPastByProgramme(
            $programme->getDbAncestryIds(),
            false,
            ApplicationTime::getTime(),
            $limit,
            $this->getOffset($limit, $page)
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts, $getFullListOfServicesForNetwork);

        return $this->mapManyEntities($broadcasts, $services);
    }

    /**
     * @return CollapsedBroadcast[]
     */
    protected function findUpcomingByProgrammeHelper(
        Programme $programme,
        ?int $limit,
        int $page,
        bool $getFullListOfServicesForNetwork
    ): array {
        $broadcasts = $this->repository->findUpcomingByProgramme(
            $programme->getDbAncestryIds(),
            false,
            ApplicationTime::getTime(),
            $limit,
            $this->getOffset($limit, $page)
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts, $getFullListOfServicesForNetwork);

        return $this->mapManyEntities($broadcasts, $services);
    }

    protected function findByBroadcastHelper(Broadcast $broadcast, bool $getFullListOfServicesForNetwork): ?CollapsedBroadcast
    {
        $broadcasts = $this->repository->findByStartAndProgrammeItemId(
            $broadcast->getStartAt(),
            $broadcast->getProgrammeItem()->getDbId()
        );

        $broadcasts = $this->stripWebcasts($broadcasts);
        $services = $this->fetchUsedServices($broadcasts, $getFullListOfServicesForNetwork);
        if (!empty($broadcasts[0])) {
            return $this->mapSingleEntity($broadcasts[0], $services);
        }
        return null;
    }

    /**
     * @return CollapsedBroadcast[]
     */
    protected function findByCategoryAndStartAtDateRangeHelper(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit,
        int $page,
        bool $getFullListOfServicesForNetwork
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
        $services = $this->fetchUsedServices($broadcasts, $getFullListOfServicesForNetwork);

        return $this->mapManyEntities($broadcasts, $services);
    }

    /**
     * @return CollapsedBroadcast[]
     */
    protected function findByCategoryAndEndAtDateRangeHelper(
        Category $category,
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate,
        ?int $limit,
        int $page,
        bool $getFullListOfServicesForNetwork
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
        $services = $this->fetchUsedServices($broadcasts, $getFullListOfServicesForNetwork);

        return $this->mapManyEntities($broadcasts, $services);
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

    private function fetchUsedServices(
        array $broadcasts,
        bool $getListOfServicesForNetwork
    ): array {
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
            if ($getListOfServicesForNetwork) {
                // In some cases (when the first broadcast and the last one have different startAt dates and one service
                // ends between these two dates) using the startAt date of the first broadcast could lead to an
                // inaccurate list of active services. However, we'll be saving many date comparisons in the future,
                // so the trade-off is worth it.
                $services = $this->serviceRepository->findByIdsWithNetworkServicesList(
                    $serviceIds,
                    $broadcasts[0]['startAt']
                );
            } else {
                $services = $this->serviceRepository->findByIds($serviceIds);
            }
        }

        // Fetch all the used services, keyed by their id
        return array_reduce($services, function ($memo, $service) {
            $memo[$service['id']] = $service;
            return $memo;
        }, []);
    }
}
