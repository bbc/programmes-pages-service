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
        $limit = self::DEFAULT_LIMIT,
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

        return array_reduce($dbYearsAndMonths, function ($memo, $period) {
            $year = (int) $period['year'];
            if (!isset($memo[$year])) {
                $memo[$year] = [];
            }

            $memo[$year][] = (int) $period['month'];

            return $memo;
        }, []);
    }

    public function findUsedDaysByCategoryInDateRange(
        Category $category,
        DateTimeImmutable $start,
        DateTimeImmutable $end,
        string $medium = null
    ): array {
        $result = $this->repository->findUsedDaysByCategoryAncestryInDateRange(
            $category->getDbAncestryIds(),
            'Broadcast',
            $medium,
            $start,
            $end
        );

        return array_reduce($result, function ($memo, $period) {
            $month = (int) $period['month'];

            if (!isset($memo[$month])) {
                $memo[$month] = [];
            }

            $memo[$month][] = (int) $period['day'];

            return $memo;
        }, []);
    }
}
