<?php

namespace BBC\ProgrammesPagesService\Service;

use BBC\ProgrammesPagesService\Data\ProgrammesDb\EntityRepository\BroadcastRepository;
use BBC\ProgrammesPagesService\Domain\Entity\Category;
use BBC\ProgrammesPagesService\Domain\Entity\Version;
use BBC\ProgrammesPagesService\Domain\Entity\Programme;
use BBC\ProgrammesPagesService\Mapper\ProgrammesDbToDomain\BroadcastMapper;
use DateTimeImmutable;

class BroadcastsService extends AbstractService
{
    public function __construct(
        BroadcastRepository $repository,
        BroadcastMapper $mapper
    ) {
        parent::__construct($repository, $mapper);
    }

    public function findByVersion(
        Version $version,
        ?int $limit = self::DEFAULT_LIMIT,
        int $page = self::DEFAULT_PAGE
    ): array {
        $dbEntities = $this->repository->findByVersion(
            [$version->getDbId()],
            'Broadcast',
            $limit,
            $this->getOffset($limit, $page)
        );

        return $this->mapManyEntities($dbEntities);
    }

    public function findBroadcastYearsAndMonthsByProgramme(Programme $programme): array
    {
        $dbYearsAndMonths = $this->repository->findAllYearsAndMonthsByProgramme(
            $programme->getDbAncestryIds(),
            'Broadcast'
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
        $dbDays = $this->repository->findBroadcastedDatesForCategories(
            [$category->getDbAncestryIds()],
            'Broadcast',
            $start,
            $end
        );

        $result = [];

        foreach ($dbDays as $day) {
            $year = (int) $day['year'];
            if (!isset($result[$year])) {
                $result[$year] = [];
            }

            $month = (int) $day['month'];
            if (!isset($result[$year][$month])) {
                $result[$year][$month] = [];
            }

            $day = (int) $day['day'];
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

        $broadcastedCategoriesAncestriesByDay = $this->repository->findBroadcastedDatesForCategories(
            $categoriesAncestryIds,
            'Broadcast',
            $from,
            $to
        );

        $broadcastedAncestries = array_column($broadcastedCategoriesAncestriesByDay, 'ancestry');

        $broadcastedCategories = [];
        foreach ($allCategories as $category) {
            $ancestryCategory = implode(',', $category->getDbAncestryIds()) . ',';
            if (in_array($ancestryCategory, $broadcastedAncestries)) {
                $broadcastedCategories[] = $category;
            }
        }

        return $broadcastedCategories;
    }
}
